import { useState } from "react";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { useLocation } from "wouter";
import { motion, AnimatePresence } from "framer-motion";
import { format } from "date-fns";
import {
  LayoutDashboard, BedDouble, CalendarRange, Users, DollarSign,
  CheckCircle2, XCircle, Clock, ChevronDown, Search, Filter,
  TrendingUp, Activity, ShieldCheck, LogOut, Home, Star
} from "lucide-react";

import { api, Booking, Room } from "@/lib/api";
import { useAuth } from "@/context/AuthContext";
import { useToast } from "@/hooks/use-toast";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Skeleton } from "@/components/ui/skeleton";
import { Input } from "@/components/ui/input";
import {
  Select, SelectContent, SelectItem, SelectTrigger, SelectValue
} from "@/components/ui/select";
import {
  AlertDialog, AlertDialogAction, AlertDialogCancel,
  AlertDialogContent, AlertDialogDescription, AlertDialogFooter,
  AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger
} from "@/components/ui/alert-dialog";
import {
  Tabs, TabsContent, TabsList, TabsTrigger
} from "@/components/ui/tabs";
import { Separator } from "@/components/ui/separator";

function StatCard({
  icon: Icon, label, value, sub, color
}: {
  icon: React.ElementType;
  label: string;
  value: string | number;
  sub?: string;
  color: string;
}) {
  return (
    <Card className="border-border/40 overflow-hidden">
      <CardContent className="p-6 flex items-start gap-4">
        <div className={`p-3 rounded-xl ${color} shrink-0`}>
          <Icon className="h-5 w-5 text-white" />
        </div>
        <div>
          <p className="text-xs text-muted-foreground uppercase tracking-widest mb-0.5">{label}</p>
          <p className="text-2xl font-serif font-bold text-foreground">{value}</p>
          {sub && <p className="text-xs text-muted-foreground mt-0.5">{sub}</p>}
        </div>
      </CardContent>
    </Card>
  );
}

function StatusBadge({ status }: { status: Booking["status"] }) {
  const map = {
    pending: { label: "Pending", icon: Clock, cls: "bg-amber-100 text-amber-800 border-amber-200" },
    confirmed: { label: "Confirmed", icon: CheckCircle2, cls: "bg-emerald-100 text-emerald-800 border-emerald-200" },
    cancelled: { label: "Cancelled", icon: XCircle, cls: "bg-red-100 text-red-700 border-red-200" },
  };
  const { label, icon: Icon, cls } = map[status] || map.pending;
  return (
    <span className={`inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border ${cls}`}>
      <Icon className="h-3 w-3" />
      {label}
    </span>
  );
}

function RoomTypeBadge({ type }: { type: string }) {
  const colors: Record<string, string> = {
    presidential: "bg-slate-800 text-white",
    deluxe: "bg-amber-700 text-white",
    suite: "bg-emerald-700 text-white",
    double: "bg-blue-700 text-white",
    single: "bg-stone-500 text-white",
  };
  return (
    <span className={`inline-block px-2.5 py-0.5 rounded-md text-xs font-semibold capitalize ${colors[type] || "bg-gray-500 text-white"}`}>
      {type}
    </span>
  );
}

export default function Admin() {
  const { user, isAuthenticated, isLoading: authLoading, logout } = useAuth();
  const [, setLocation] = useLocation();
  const { toast } = useToast();
  const queryClient = useQueryClient();
  const [search, setSearch] = useState("");
  const [statusFilter, setStatusFilter] = useState("all");
  const [typeFilter, setTypeFilter] = useState("all");

  if (!authLoading && !isAuthenticated) {
    setLocation("/login");
    return null;
  }

  if (!authLoading && user && user.role !== "admin") {
    return (
      <div className="min-h-[70vh] flex flex-col items-center justify-center text-center px-4">
        <ShieldCheck className="h-16 w-16 text-muted-foreground/30 mb-4" />
        <h2 className="font-serif text-3xl font-bold text-primary mb-2">Access Restricted</h2>
        <p className="text-muted-foreground mb-6">
          This area is reserved for hotel administrators only.
        </p>
        <Button onClick={() => setLocation("/dashboard")}>Go to My Dashboard</Button>
      </div>
    );
  }

  const { data: bookings = [], isLoading: bookingsLoading } = useQuery({
    queryKey: ["admin-bookings"],
    queryFn: api.getMyBookings,
    enabled: isAuthenticated,
  });

  const { data: rooms = [], isLoading: roomsLoading } = useQuery({
    queryKey: ["admin-rooms"],
    queryFn: () => api.getRooms({ per_page: "50" }),
    enabled: isAuthenticated,
  });

  const cancelMutation = useMutation({
    mutationFn: (id: number) => api.cancelBooking(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["admin-bookings"] });
      toast({ title: "Booking cancelled", description: "The booking has been cancelled." });
    },
    onError: (err: any) => {
      toast({ variant: "destructive", title: "Error", description: err.message });
    },
  });

  const totalRevenue = bookings
    .filter((b) => b.status !== "cancelled")
    .reduce((sum, b) => sum + parseFloat(b.total_price), 0);

  const confirmedCount = bookings.filter((b) => b.status === "confirmed").length;
  const pendingCount = bookings.filter((b) => b.status === "pending").length;
  const cancelledCount = bookings.filter((b) => b.status === "cancelled").length;
  const activeRooms = rooms.filter((r) => r.status === "active").length;

  const filteredBookings = bookings.filter((b) => {
    const matchesSearch =
      !search ||
      b.booking_reference.toLowerCase().includes(search.toLowerCase()) ||
      b.guest_name?.toLowerCase().includes(search.toLowerCase()) ||
      b.guest_email?.toLowerCase().includes(search.toLowerCase()) ||
      b.room_number?.includes(search);
    const matchesStatus = statusFilter === "all" || b.status === statusFilter;
    const matchesType = typeFilter === "all" || b.room_type === typeFilter;
    return matchesSearch && matchesStatus && matchesType;
  });

  const roomTypes = [...new Set(bookings.map((b) => b.room_type).filter(Boolean))];

  if (authLoading) {
    return (
      <div className="container mx-auto px-4 py-12">
        <div className="space-y-4">
          {Array(4).fill(0).map((_, i) => (
            <Skeleton key={i} className="h-24 w-full rounded-xl" />
          ))}
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-background">
      <div className="border-b border-border/50 bg-card/60 backdrop-blur-sm sticky top-0 z-10">
        <div className="container mx-auto px-4 py-4 flex items-center justify-between">
          <div className="flex items-center gap-3">
            <div className="p-2 bg-primary/10 rounded-lg">
              <LayoutDashboard className="h-5 w-5 text-primary" />
            </div>
            <div>
              <h1 className="font-serif text-xl font-bold text-primary">Admin Panel</h1>
              <p className="text-xs text-muted-foreground">Grand Palais Hotel</p>
            </div>
          </div>
          <div className="flex items-center gap-2">
            <Button variant="ghost" size="sm" onClick={() => setLocation("/")} className="hidden sm:flex">
              <Home className="h-4 w-4 mr-1.5" /> Site
            </Button>
            <Button variant="ghost" size="sm" onClick={() => logout().then(() => setLocation("/"))}>
              <LogOut className="h-4 w-4 mr-1.5" /> Sign out
            </Button>
          </div>
        </div>
      </div>

      <div className="container mx-auto px-4 py-8 space-y-8">
        <motion.div
          initial={{ opacity: 0, y: 12 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.4 }}
        >
          <div className="mb-2">
            <span className="text-sm text-muted-foreground">Welcome back,</span>{" "}
            <span className="text-sm font-medium">{user?.name}</span>
          </div>
          <h2 className="font-serif text-3xl font-bold text-foreground mb-6">Overview</h2>

          <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
            <StatCard
              icon={DollarSign}
              label="Total Revenue"
              value={`$${totalRevenue.toLocaleString("en-US", { minimumFractionDigits: 0, maximumFractionDigits: 0 })}`}
              sub="Active bookings only"
              color="bg-emerald-600"
            />
            <StatCard
              icon={CalendarRange}
              label="Total Bookings"
              value={bookings.length}
              sub={`${pendingCount} pending`}
              color="bg-blue-600"
            />
            <StatCard
              icon={BedDouble}
              label="Active Rooms"
              value={`${activeRooms} / ${rooms.length}`}
              sub="Available inventory"
              color="bg-violet-600"
            />
            <StatCard
              icon={TrendingUp}
              label="Confirmed"
              value={confirmedCount}
              sub={`${cancelledCount} cancelled`}
              color="bg-amber-600"
            />
          </div>
        </motion.div>

        <Tabs defaultValue="bookings">
          <TabsList className="bg-muted/50 border border-border/40 p-1 rounded-xl h-auto mb-6">
            <TabsTrigger value="bookings" className="rounded-lg px-5 py-2 text-sm">
              <CalendarRange className="h-4 w-4 mr-2" />
              Bookings
              {pendingCount > 0 && (
                <span className="ml-2 bg-amber-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                  {pendingCount}
                </span>
              )}
            </TabsTrigger>
            <TabsTrigger value="rooms" className="rounded-lg px-5 py-2 text-sm">
              <BedDouble className="h-4 w-4 mr-2" />
              Rooms
            </TabsTrigger>
          </TabsList>

          <TabsContent value="bookings">
            <Card className="border-border/40">
              <CardHeader className="pb-4">
                <div className="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
                  <CardTitle className="font-serif text-xl">All Reservations</CardTitle>
                  <div className="flex flex-wrap gap-2 w-full sm:w-auto">
                    <div className="relative flex-1 sm:w-56">
                      <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                      <Input
                        placeholder="Search reference, guest..."
                        className="pl-8 h-9 text-sm"
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                      />
                    </div>
                    <Select value={statusFilter} onValueChange={setStatusFilter}>
                      <SelectTrigger className="h-9 w-32 text-sm">
                        <Filter className="h-3.5 w-3.5 mr-1.5 text-muted-foreground" />
                        <SelectValue />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="all">All Status</SelectItem>
                        <SelectItem value="pending">Pending</SelectItem>
                        <SelectItem value="confirmed">Confirmed</SelectItem>
                        <SelectItem value="cancelled">Cancelled</SelectItem>
                      </SelectContent>
                    </Select>
                    {roomTypes.length > 0 && (
                      <Select value={typeFilter} onValueChange={setTypeFilter}>
                        <SelectTrigger className="h-9 w-32 text-sm">
                          <SelectValue placeholder="Room type" />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectItem value="all">All Types</SelectItem>
                          {roomTypes.map((t) => (
                            <SelectItem key={t} value={t} className="capitalize">{t}</SelectItem>
                          ))}
                        </SelectContent>
                      </Select>
                    )}
                  </div>
                </div>
              </CardHeader>
              <CardContent className="p-0">
                {bookingsLoading ? (
                  <div className="p-6 space-y-3">
                    {Array(5).fill(0).map((_, i) => (
                      <Skeleton key={i} className="h-14 w-full rounded-lg" />
                    ))}
                  </div>
                ) : filteredBookings.length === 0 ? (
                  <div className="text-center py-16 text-muted-foreground">
                    <CalendarRange className="h-10 w-10 mx-auto mb-3 opacity-30" />
                    <p className="font-medium">No bookings found</p>
                    <p className="text-sm">Try adjusting your filters</p>
                  </div>
                ) : (
                  <div className="overflow-x-auto">
                    <table className="w-full text-sm">
                      <thead>
                        <tr className="border-b border-border/50 bg-muted/30">
                          <th className="text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground px-4 py-3">Reference</th>
                          <th className="text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground px-4 py-3 hidden md:table-cell">Guest</th>
                          <th className="text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground px-4 py-3">Room</th>
                          <th className="text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground px-4 py-3 hidden sm:table-cell">Dates</th>
                          <th className="text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground px-4 py-3">Total</th>
                          <th className="text-left text-xs font-semibold uppercase tracking-wider text-muted-foreground px-4 py-3">Status</th>
                          <th className="text-right text-xs font-semibold uppercase tracking-wider text-muted-foreground px-4 py-3">Actions</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-border/30">
                        <AnimatePresence>
                          {filteredBookings.map((booking) => (
                            <motion.tr
                              key={booking.id}
                              initial={{ opacity: 0 }}
                              animate={{ opacity: 1 }}
                              exit={{ opacity: 0 }}
                              className="hover:bg-muted/20 transition-colors"
                            >
                              <td className="px-4 py-3">
                                <span className="font-mono text-xs font-semibold text-primary">{booking.booking_reference}</span>
                              </td>
                              <td className="px-4 py-3 hidden md:table-cell">
                                <div>
                                  <p className="font-medium text-foreground truncate max-w-[150px]">{booking.guest_name || "—"}</p>
                                  <p className="text-xs text-muted-foreground truncate max-w-[150px]">{booking.guest_email || ""}</p>
                                </div>
                              </td>
                              <td className="px-4 py-3">
                                <div className="flex flex-col gap-1">
                                  <RoomTypeBadge type={booking.room_type} />
                                  <span className="text-xs text-muted-foreground">#{booking.room_number}</span>
                                </div>
                              </td>
                              <td className="px-4 py-3 hidden sm:table-cell">
                                <div className="text-xs text-muted-foreground space-y-0.5">
                                  <div>{booking.check_in ? format(new Date(booking.check_in), "MMM d, yyyy") : "—"}</div>
                                  <div className="text-muted-foreground/60">→ {booking.check_out ? format(new Date(booking.check_out), "MMM d, yyyy") : "—"}</div>
                                </div>
                              </td>
                              <td className="px-4 py-3">
                                <span className="font-semibold text-primary">${booking.total_price}</span>
                              </td>
                              <td className="px-4 py-3">
                                <StatusBadge status={booking.status} />
                              </td>
                              <td className="px-4 py-3 text-right">
                                {booking.status !== "cancelled" && (
                                  <AlertDialog>
                                    <AlertDialogTrigger asChild>
                                      <Button variant="outline" size="sm" className="text-destructive border-destructive/30 hover:bg-destructive/5 text-xs h-7">
                                        Cancel
                                      </Button>
                                    </AlertDialogTrigger>
                                    <AlertDialogContent>
                                      <AlertDialogHeader>
                                        <AlertDialogTitle className="font-serif">Cancel this booking?</AlertDialogTitle>
                                        <AlertDialogDescription>
                                          Cancel booking <span className="font-mono font-semibold">{booking.booking_reference}</span> for{" "}
                                          {booking.guest_name}? This cannot be undone.
                                        </AlertDialogDescription>
                                      </AlertDialogHeader>
                                      <AlertDialogFooter>
                                        <AlertDialogCancel>Keep it</AlertDialogCancel>
                                        <AlertDialogAction
                                          onClick={() => cancelMutation.mutate(booking.id)}
                                          className="bg-destructive hover:bg-destructive/90"
                                        >
                                          Cancel booking
                                        </AlertDialogAction>
                                      </AlertDialogFooter>
                                    </AlertDialogContent>
                                  </AlertDialog>
                                )}
                              </td>
                            </motion.tr>
                          ))}
                        </AnimatePresence>
                      </tbody>
                    </table>
                    <div className="px-4 py-3 border-t border-border/30 text-xs text-muted-foreground">
                      Showing {filteredBookings.length} of {bookings.length} bookings
                    </div>
                  </div>
                )}
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="rooms">
            <Card className="border-border/40">
              <CardHeader>
                <CardTitle className="font-serif text-xl">Room Inventory</CardTitle>
              </CardHeader>
              <CardContent className="p-0">
                {roomsLoading ? (
                  <div className="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    {Array(6).fill(0).map((_, i) => (
                      <Skeleton key={i} className="h-36 w-full rounded-xl" />
                    ))}
                  </div>
                ) : rooms.length === 0 ? (
                  <div className="text-center py-16 text-muted-foreground">
                    <BedDouble className="h-10 w-10 mx-auto mb-3 opacity-30" />
                    <p>No rooms found</p>
                  </div>
                ) : (
                  <div className="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    {rooms.map((room) => (
                      <motion.div
                        key={room.id}
                        initial={{ opacity: 0, y: 8 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.3 }}
                      >
                        <Card className="border-border/40 hover:shadow-sm transition-shadow overflow-hidden">
                          <div className="h-2 w-full" style={{
                            background: room.status === "active"
                              ? "linear-gradient(90deg, #10b981, #059669)"
                              : "linear-gradient(90deg, #9ca3af, #6b7280)"
                          }} />
                          <CardContent className="p-4">
                            <div className="flex items-start justify-between mb-3">
                              <div>
                                <div className="flex items-center gap-2 mb-1">
                                  <span className="font-mono font-bold text-lg text-primary">#{room.room_number}</span>
                                  <RoomTypeBadge type={room.type} />
                                </div>
                                <p className="text-xs text-muted-foreground">Floor {room.floor}</p>
                              </div>
                              <span className={`inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold ${
                                room.status === "active"
                                  ? "bg-emerald-100 text-emerald-700"
                                  : "bg-gray-100 text-gray-600"
                              }`}>
                                <Activity className="h-3 w-3" />
                                {room.status}
                              </span>
                            </div>
                            <Separator className="my-3" />
                            <div className="grid grid-cols-2 gap-2 text-xs">
                              <div>
                                <p className="text-muted-foreground mb-0.5">Capacity</p>
                                <div className="flex items-center gap-1 font-medium">
                                  <Users className="h-3 w-3" />
                                  {room.capacity} guests
                                </div>
                              </div>
                              <div>
                                <p className="text-muted-foreground mb-0.5">Rate</p>
                                <div className="flex items-center gap-1 font-semibold text-primary">
                                  <DollarSign className="h-3 w-3" />
                                  ${room.price_per_night}<span className="font-normal text-muted-foreground">/night</span>
                                </div>
                              </div>
                            </div>
                            {room.amenities && (
                              <p className="text-[11px] text-muted-foreground mt-3 line-clamp-1 bg-muted/30 rounded px-2 py-1">
                                {room.amenities}
                              </p>
                            )}
                          </CardContent>
                        </Card>
                      </motion.div>
                    ))}
                  </div>
                )}
              </CardContent>
            </Card>
          </TabsContent>
        </Tabs>
      </div>
    </div>
  );
}
