import { useState, useMemo } from "react";
import { useQuery, useMutation } from "@tanstack/react-query";
import { useParams, useLocation } from "wouter";
import { motion } from "framer-motion";
import { format, differenceInDays } from "date-fns";
import { DayPicker } from "react-day-picker";
import "react-day-picker/dist/style.css";
import {
  BedDouble, Users, Calendar, Wifi, Tv, Wind, Coffee, Star,
  ChevronLeft, ArrowRight, CheckCircle
} from "lucide-react";

import { api, Room } from "@/lib/api";
import { useAuth } from "@/context/AuthContext";
import { useToast } from "@/hooks/use-toast";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent } from "@/components/ui/card";
import { Skeleton } from "@/components/ui/skeleton";
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";
import { Label } from "@/components/ui/label";
import { cn } from "@/lib/utils";

const roomGradients: Record<string, string> = {
  presidential: 'from-slate-900 via-slate-800 to-slate-700',
  deluxe: 'from-amber-950 via-amber-900 to-amber-800',
  suite: 'from-emerald-950 via-emerald-900 to-emerald-800',
  double: 'from-blue-950 via-blue-900 to-blue-800',
  single: 'from-stone-700 via-stone-600 to-stone-500',
};

const amenityIcons: Record<string, typeof Wifi> = {
  WiFi: Wifi,
  TV: Tv,
  'Smart TV': Tv,
  'Air Conditioning': Wind,
  'Mini-bar': Coffee,
};

function AmenityBadge({ amenity }: { amenity: string }) {
  const trimmed = amenity.trim();
  const Icon = amenityIcons[trimmed] || Star;
  return (
    <div className="flex items-center gap-2 bg-muted/50 rounded-lg px-3 py-2">
      <Icon className="h-4 w-4 text-primary shrink-0" />
      <span className="text-sm">{trimmed}</span>
    </div>
  );
}

export default function RoomDetail() {
  const { id } = useParams<{ id: string }>();
  const { isAuthenticated } = useAuth();
  const [, setLocation] = useLocation();
  const { toast } = useToast();

  const [dateRange, setDateRange] = useState<{ from?: Date; to?: Date }>({});
  const [guests, setGuests] = useState("1");
  const [specialRequests, setSpecialRequests] = useState("");

  const { data: room, isLoading } = useQuery<Room>({
    queryKey: ["room", id],
    queryFn: () => api.getRoom(id!),
    enabled: !!id,
  });

  const nights = useMemo(() => {
    if (dateRange.from && dateRange.to) {
      return differenceInDays(dateRange.to, dateRange.from);
    }
    return 0;
  }, [dateRange]);

  const totalPrice = useMemo(() => {
    if (room && nights > 0) {
      return (parseFloat(room.price_per_night) * nights).toFixed(2);
    }
    return null;
  }, [room, nights]);

  const bookMutation = useMutation({
    mutationFn: () => api.createBooking({
      room_id: room!.id,
      check_in: format(dateRange.from!, 'yyyy-MM-dd'),
      check_out: format(dateRange.to!, 'yyyy-MM-dd'),
      guests: parseInt(guests),
      special_requests: specialRequests,
    }),
    onSuccess: (booking) => {
      toast({
        title: "Booking confirmed!",
        description: `Your booking ${booking.booking_reference} is confirmed. See you soon!`,
      });
      setLocation("/dashboard");
    },
    onError: (err: any) => {
      toast({
        variant: "destructive",
        title: "Booking failed",
        description: err.message || "Something went wrong. Please try again.",
      });
    },
  });

  const handleBook = () => {
    if (!isAuthenticated) {
      toast({
        title: "Please sign in",
        description: "You need to be logged in to make a booking.",
      });
      setLocation("/login");
      return;
    }
    if (!dateRange.from || !dateRange.to) {
      toast({ variant: "destructive", title: "Select dates", description: "Please choose your check-in and check-out dates." });
      return;
    }
    if (nights < 1) {
      toast({ variant: "destructive", title: "Invalid dates", description: "Check-out must be after check-in." });
      return;
    }
    bookMutation.mutate();
  };

  if (isLoading) {
    return (
      <div className="container mx-auto px-4 py-12">
        <Skeleton className="h-80 w-full rounded-2xl mb-8" />
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <div className="lg:col-span-2 space-y-4">
            <Skeleton className="h-10 w-1/3" />
            <Skeleton className="h-4 w-full" />
            <Skeleton className="h-4 w-2/3" />
          </div>
          <Skeleton className="h-64 rounded-xl" />
        </div>
      </div>
    );
  }

  if (!room) return null;

  const gradient = roomGradients[room.type] || 'from-stone-700 to-stone-500';
  const amenities = room.amenities ? room.amenities.split(',') : [];
  const maxGuests = Array.from({ length: room.capacity }, (_, i) => i + 1);

  return (
    <div className="min-h-screen">
      {/* Hero */}
      <motion.div
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        className={`h-72 md:h-96 bg-gradient-to-br ${gradient} relative`}
      >
        <div className="absolute inset-0 bg-black/20" />
        <div className="container mx-auto px-4 h-full flex flex-col justify-end pb-8 relative z-10">
          <button
            onClick={() => setLocation("/rooms")}
            className="flex items-center gap-1.5 text-white/70 hover:text-white transition-colors text-sm mb-4 w-fit"
            data-testid="btn-back-rooms"
          >
            <ChevronLeft className="h-4 w-4" /> All Rooms
          </button>
          <div className="flex flex-wrap items-end gap-4">
            <div>
              <Badge variant="outline" className="mb-2 border-white/30 text-white text-xs uppercase tracking-widest">
                Room {room.room_number} · Floor {room.floor}
              </Badge>
              <h1 className="font-serif text-4xl md:text-5xl font-bold text-white capitalize">{room.type} Room</h1>
            </div>
            <div className="ml-auto bg-white/10 backdrop-blur border border-white/20 rounded-xl px-5 py-3 text-right">
              <p className="text-white/70 text-xs uppercase tracking-wider mb-0.5">Per Night</p>
              <p className="text-3xl font-serif font-bold text-white">${room.price_per_night}</p>
            </div>
          </div>
        </div>
      </motion.div>

      <div className="container mx-auto px-4 py-12">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-10">
          {/* Details */}
          <div className="lg:col-span-2 space-y-8">
            <motion.div initial={{ opacity: 0, y: 16 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: 0.1 }}>
              <div className="flex flex-wrap gap-4 mb-6">
                <div className="flex items-center gap-2 text-sm">
                  <Users className="h-4 w-4 text-primary" />
                  <span>Up to {room.capacity} {room.capacity === 1 ? 'guest' : 'guests'}</span>
                </div>
                <div className="flex items-center gap-2 text-sm">
                  <BedDouble className="h-4 w-4 text-primary" />
                  <span className="capitalize">{room.type} accommodation</span>
                </div>
              </div>

              <p className="text-foreground/80 leading-relaxed text-base">
                {room.description || `Experience the finest in luxury accommodation with our carefully appointed ${room.type} room. Designed for comfort and elegance, every detail has been thoughtfully considered for your perfect stay at Grand Palais.`}
              </p>
            </motion.div>

            {amenities.length > 0 && (
              <motion.div initial={{ opacity: 0, y: 16 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: 0.2 }}>
                <h2 className="font-serif text-2xl font-bold text-primary mb-4">Amenities</h2>
                <div className="grid grid-cols-2 sm:grid-cols-3 gap-3">
                  {amenities.map((amenity, i) => (
                    <AmenityBadge key={i} amenity={amenity} />
                  ))}
                </div>
              </motion.div>
            )}

            <motion.div initial={{ opacity: 0, y: 16 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: 0.3 }}>
              <h2 className="font-serif text-2xl font-bold text-primary mb-4">The Grand Palais Promise</h2>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {[
                  "24-hour concierge service",
                  "Daily housekeeping",
                  "Complimentary welcome amenities",
                  "Flexible check-in and check-out",
                ].map((promise, i) => (
                  <div key={i} className="flex items-center gap-2 text-sm">
                    <CheckCircle className="h-4 w-4 text-emerald-600 shrink-0" />
                    <span>{promise}</span>
                  </div>
                ))}
              </div>
            </motion.div>
          </div>

          {/* Booking Card */}
          <motion.div initial={{ opacity: 0, x: 16 }} animate={{ opacity: 1, x: 0 }} transition={{ delay: 0.2 }}>
            <Card className="border-border/50 shadow-lg sticky top-24">
              <CardContent className="p-6 space-y-5">
                <div>
                  <h3 className="font-serif text-xl font-bold text-primary mb-1">Reserve Your Stay</h3>
                  <p className="text-sm text-muted-foreground">Fill in the details below to book.</p>
                </div>

                <div className="space-y-2">
                  <Label className="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Dates</Label>
                  <Popover>
                    <PopoverTrigger asChild>
                      <Button
                        variant="outline"
                        className={cn("w-full justify-start text-left font-normal h-11", !dateRange.from && "text-muted-foreground")}
                        data-testid="btn-date-picker"
                      >
                        <Calendar className="mr-2 h-4 w-4 text-primary" />
                        {dateRange.from ? (
                          dateRange.to ? (
                            <>{format(dateRange.from, "MMM d")} — {format(dateRange.to, "MMM d, yyyy")}</>
                          ) : format(dateRange.from, "MMM d, yyyy")
                        ) : "Select dates"}
                      </Button>
                    </PopoverTrigger>
                    <PopoverContent className="w-auto p-0" align="start">
                      <DayPicker
                        mode="range"
                        selected={{ from: dateRange.from, to: dateRange.to }}
                        onSelect={(range) => setDateRange({ from: range?.from, to: range?.to })}
                        disabled={{ before: new Date() }}
                        numberOfMonths={1}
                      />
                    </PopoverContent>
                  </Popover>
                </div>

                <div className="space-y-2">
                  <Label className="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Guests</Label>
                  <Select value={guests} onValueChange={setGuests}>
                    <SelectTrigger className="h-11" data-testid="select-guests">
                      <div className="flex items-center gap-2">
                        <Users className="h-4 w-4 text-primary" />
                        <SelectValue />
                      </div>
                    </SelectTrigger>
                    <SelectContent>
                      {maxGuests.map(n => (
                        <SelectItem key={n} value={String(n)}>{n} {n === 1 ? 'Guest' : 'Guests'}</SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>

                <div className="space-y-2">
                  <Label className="text-xs font-semibold uppercase tracking-wider text-muted-foreground">
                    Special Requests <span className="font-normal normal-case">(optional)</span>
                  </Label>
                  <Textarea
                    placeholder="Any special requests or preferences..."
                    value={specialRequests}
                    onChange={(e) => setSpecialRequests(e.target.value)}
                    className="resize-none h-20 text-sm"
                    data-testid="input-special-requests"
                  />
                </div>

                {totalPrice && (
                  <div className="bg-muted/50 rounded-xl p-4 space-y-2">
                    <div className="flex justify-between text-sm">
                      <span className="text-muted-foreground">${room.price_per_night} × {nights} nights</span>
                      <span className="font-medium">${totalPrice}</span>
                    </div>
                    <div className="border-t border-border pt-2 flex justify-between font-bold text-primary">
                      <span>Total</span>
                      <span>${totalPrice}</span>
                    </div>
                  </div>
                )}

                <Button
                  className="w-full h-12 text-base font-semibold"
                  onClick={handleBook}
                  disabled={bookMutation.isPending}
                  data-testid="btn-confirm-booking"
                >
                  {bookMutation.isPending ? (
                    <span className="flex items-center gap-2">
                      <span className="h-4 w-4 rounded-full border-2 border-current border-t-transparent animate-spin" />
                      Confirming...
                    </span>
                  ) : (
                    <span className="flex items-center gap-2">
                      {isAuthenticated ? "Confirm Booking" : "Sign In to Book"}
                      <ArrowRight className="h-4 w-4" />
                    </span>
                  )}
                </Button>

                {!isAuthenticated && (
                  <p className="text-xs text-center text-muted-foreground">
                    You'll be redirected to sign in first.
                  </p>
                )}
              </CardContent>
            </Card>
          </motion.div>
        </div>
      </div>
    </div>
  );
}
