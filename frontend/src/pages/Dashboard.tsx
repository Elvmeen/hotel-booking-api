import { useState } from "react";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { useLocation } from "wouter";
import { motion, AnimatePresence } from "framer-motion";
import { format } from "date-fns";
import { CalendarDays, BedDouble, Hash, Users, XCircle, CheckCircle2, Clock, AlertCircle } from "lucide-react";

import { api, Booking } from "@/lib/api";
import { useAuth } from "@/context/AuthContext";
import { useToast } from "@/hooks/use-toast";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Skeleton } from "@/components/ui/skeleton";
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from "@/components/ui/alert-dialog";

function StatusBadge({ status }: { status: Booking['status'] }) {
  const variants = {
    pending: { icon: Clock, label: 'Pending', className: 'bg-amber-100 text-amber-800 border-amber-200' },
    confirmed: { icon: CheckCircle2, label: 'Confirmed', className: 'bg-emerald-100 text-emerald-800 border-emerald-200' },
    cancelled: { icon: XCircle, label: 'Cancelled', className: 'bg-red-100 text-red-700 border-red-200' },
  };
  const { icon: Icon, label, className } = variants[status] || variants.pending;

  return (
    <span className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border ${className}`}>
      <Icon className="h-3.5 w-3.5" />
      {label}
    </span>
  );
}

function BookingCard({ booking, onCancel }: { booking: Booking; onCancel: (id: number) => void }) {
  const roomGradients: Record<string, string> = {
    presidential: 'from-slate-800 to-slate-600',
    deluxe: 'from-amber-800 to-amber-600',
    suite: 'from-emerald-800 to-emerald-600',
    double: 'from-blue-800 to-blue-600',
    single: 'from-stone-600 to-stone-400',
  };
  const gradient = roomGradients[booking.room_type] || 'from-stone-600 to-stone-400';

  return (
    <motion.div
      layout
      initial={{ opacity: 0, y: 12 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, scale: 0.97 }}
      transition={{ duration: 0.3 }}
    >
      <Card className="overflow-hidden border-border/50 hover:shadow-md transition-shadow" data-testid={`card-booking-${booking.id}`}>
        <div className="flex flex-col sm:flex-row">
          <div className={`w-full sm:w-24 h-24 sm:h-auto bg-gradient-to-br ${gradient} flex items-center justify-center shrink-0`}>
            <BedDouble className="h-8 w-8 text-white/60" />
          </div>
          <CardContent className="p-5 flex-1">
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
              <div>
                <div className="flex items-center gap-2 mb-1">
                  <h3 className="font-serif text-lg font-bold text-primary capitalize">{booking.room_type} Room</h3>
                  <span className="text-muted-foreground text-sm">#{booking.room_number}</span>
                </div>
                <div className="flex items-center gap-1.5 text-xs text-muted-foreground">
                  <Hash className="h-3 w-3" />
                  <span className="font-mono font-medium">{booking.booking_reference}</span>
                </div>
              </div>
              <StatusBadge status={booking.status} />
            </div>

            <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
              <div>
                <p className="text-xs text-muted-foreground uppercase tracking-wider mb-0.5">Check-in</p>
                <div className="flex items-center gap-1.5 text-sm font-medium">
                  <CalendarDays className="h-3.5 w-3.5 text-primary" />
                  {format(new Date(booking.check_in), 'MMM d, yyyy')}
                </div>
              </div>
              <div>
                <p className="text-xs text-muted-foreground uppercase tracking-wider mb-0.5">Check-out</p>
                <div className="flex items-center gap-1.5 text-sm font-medium">
                  <CalendarDays className="h-3.5 w-3.5 text-primary" />
                  {format(new Date(booking.check_out), 'MMM d, yyyy')}
                </div>
              </div>
              <div>
                <p className="text-xs text-muted-foreground uppercase tracking-wider mb-0.5">Guests</p>
                <div className="flex items-center gap-1.5 text-sm font-medium">
                  <Users className="h-3.5 w-3.5 text-primary" />
                  {booking.guests} {booking.guests === 1 ? 'Guest' : 'Guests'}
                </div>
              </div>
              <div>
                <p className="text-xs text-muted-foreground uppercase tracking-wider mb-0.5">Total</p>
                <p className="text-sm font-bold text-primary">${booking.total_price}</p>
              </div>
            </div>

            {booking.special_requests && (
              <p className="text-xs text-muted-foreground bg-muted/40 rounded-lg px-3 py-2 mb-4 line-clamp-2">
                Note: {booking.special_requests}
              </p>
            )}

            {booking.status === 'pending' && (
              <div className="flex justify-end">
                <AlertDialog>
                  <AlertDialogTrigger asChild>
                    <Button variant="outline" size="sm" className="text-destructive border-destructive/30 hover:bg-destructive/5" data-testid={`btn-cancel-${booking.id}`}>
                      <XCircle className="h-3.5 w-3.5 mr-1.5" />
                      Cancel Booking
                    </Button>
                  </AlertDialogTrigger>
                  <AlertDialogContent>
                    <AlertDialogHeader>
                      <AlertDialogTitle className="font-serif">Cancel this booking?</AlertDialogTitle>
                      <AlertDialogDescription>
                        You are about to cancel booking <span className="font-mono font-semibold">{booking.booking_reference}</span> for a {booking.room_type} room. This action cannot be undone.
                      </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                      <AlertDialogCancel>Keep Booking</AlertDialogCancel>
                      <AlertDialogAction
                        onClick={() => onCancel(booking.id)}
                        className="bg-destructive hover:bg-destructive/90"
                        data-testid={`btn-confirm-cancel-${booking.id}`}
                      >
                        Yes, Cancel
                      </AlertDialogAction>
                    </AlertDialogFooter>
                  </AlertDialogContent>
                </AlertDialog>
              </div>
            )}
          </CardContent>
        </div>
      </Card>
    </motion.div>
  );
}

export default function Dashboard() {
  const { user, isAuthenticated, isLoading: authLoading } = useAuth();
  const [, setLocation] = useLocation();
  const { toast } = useToast();
  const queryClient = useQueryClient();

  if (!authLoading && !isAuthenticated) {
    setLocation("/login");
    return null;
  }

  const { data: bookings, isLoading } = useQuery({
    queryKey: ["my-bookings"],
    queryFn: api.getMyBookings,
    enabled: isAuthenticated,
  });

  const cancelMutation = useMutation({
    mutationFn: (id: number) => api.cancelBooking(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["my-bookings"] });
      toast({ title: "Booking cancelled", description: "Your booking has been successfully cancelled." });
    },
    onError: (err: any) => {
      toast({ variant: "destructive", title: "Could not cancel", description: err.message });
    },
  });

  const pendingCount = bookings?.filter(b => b.status === 'pending').length || 0;
  const confirmedCount = bookings?.filter(b => b.status === 'confirmed').length || 0;
  const totalSpent = bookings?.filter(b => b.status !== 'cancelled').reduce((sum, b) => sum + parseFloat(b.total_price), 0) || 0;

  return (
    <div className="container mx-auto px-4 py-12">
      <motion.div initial={{ opacity: 0, y: 16 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.4 }}>
        <div className="mb-10">
          <h1 className="font-serif text-4xl font-bold text-primary mb-2">My Bookings</h1>
          {user && (
            <p className="text-muted-foreground">Welcome back, <span className="font-medium text-foreground">{user.name}</span>. Here are your reservations.</p>
          )}
        </div>

        {!isLoading && bookings && bookings.length > 0 && (
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
            <Card className="border-border/50">
              <CardContent className="p-5">
                <p className="text-xs uppercase tracking-widest text-muted-foreground mb-1">Pending</p>
                <p className="text-3xl font-serif font-bold text-amber-600">{pendingCount}</p>
              </CardContent>
            </Card>
            <Card className="border-border/50">
              <CardContent className="p-5">
                <p className="text-xs uppercase tracking-widest text-muted-foreground mb-1">Confirmed</p>
                <p className="text-3xl font-serif font-bold text-emerald-600">{confirmedCount}</p>
              </CardContent>
            </Card>
            <Card className="border-border/50">
              <CardContent className="p-5">
                <p className="text-xs uppercase tracking-widest text-muted-foreground mb-1">Total Spent</p>
                <p className="text-3xl font-serif font-bold text-primary">${totalSpent.toFixed(2)}</p>
              </CardContent>
            </Card>
          </div>
        )}

        {isLoading ? (
          <div className="space-y-4">
            {Array(3).fill(0).map((_, i) => (
              <div key={i} className="flex gap-4">
                <Skeleton className="h-24 w-24 rounded-xl shrink-0" />
                <div className="flex-1 space-y-2">
                  <Skeleton className="h-5 w-1/3" />
                  <Skeleton className="h-4 w-1/4" />
                  <Skeleton className="h-4 w-2/3" />
                </div>
              </div>
            ))}
          </div>
        ) : bookings?.length === 0 ? (
          <div className="text-center py-20 bg-card rounded-2xl border border-dashed border-border">
            <AlertCircle className="h-12 w-12 mx-auto text-muted-foreground opacity-50 mb-4" />
            <h3 className="font-serif text-2xl text-primary mb-2">No bookings yet</h3>
            <p className="text-muted-foreground mb-6">Ready to experience the Grand Palais difference?</p>
            <Button asChild data-testid="btn-browse-rooms">
              <a href="/rooms">Browse Rooms</a>
            </Button>
          </div>
        ) : (
          <div className="space-y-4">
            <AnimatePresence>
              {bookings?.map(booking => (
                <BookingCard
                  key={booking.id}
                  booking={booking}
                  onCancel={(id) => cancelMutation.mutate(id)}
                />
              ))}
            </AnimatePresence>
          </div>
        )}
      </motion.div>
    </div>
  );
}
