import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { Link, useLocation } from "wouter";
import { format } from "date-fns";
import { Calendar as CalendarIcon, Users, ArrowRight } from "lucide-react";
import { DayPicker } from "react-day-picker";
import "react-day-picker/dist/style.css";
import { motion } from "framer-motion";

import { api } from "@/lib/api";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { cn } from "@/lib/utils";
import { Badge } from "@/components/ui/badge";

export default function Home() {
  const [, setLocation] = useLocation();
  const [dateRange, setDateRange] = useState<{ from?: Date; to?: Date }>({});
  const [guests, setGuests] = useState("1");

  const handleSearch = () => {
    const params = new URLSearchParams();
    if (guests) params.set("capacity", guests);
    setLocation(`/rooms?${params.toString()}`);
  };

  const { data: featuredRooms, isLoading } = useQuery({
    queryKey: ["rooms", "featured"],
    queryFn: () => api.getRooms({ limit: "3" }),
  });

  return (
    <div className="flex flex-col min-h-screen">
      {/* Hero Section */}
      <section className="relative h-[85vh] w-full flex items-center justify-center overflow-hidden">
        <div className="absolute inset-0 z-0">
          <div className="w-full h-full bg-gradient-to-br from-slate-900 via-blue-950 to-amber-950" />
          <div className="absolute inset-0 bg-black/30" />
        </div>
        
        <div className="container relative z-10 mx-auto px-4 flex flex-col items-center text-center mt-16">
          <motion.div
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, ease: "easeOut" }}
          >
            <Badge variant="outline" className="mb-6 text-white border-white/30 backdrop-blur-sm px-4 py-1 text-sm tracking-widest uppercase">
              Welcome to Luxury
            </Badge>
            <h1 className="text-5xl md:text-7xl lg:text-8xl font-serif font-bold text-white mb-6 drop-shadow-lg">
              Grand Palais
            </h1>
            <p className="text-lg md:text-xl text-white/90 max-w-2xl mx-auto mb-12 font-light tracking-wide">
              An unhurried boutique experience where elegance meets modern comfort.
            </p>
          </motion.div>

          {/* Search Box */}
          <motion.div 
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, delay: 0.2, ease: "easeOut" }}
            className="bg-background/95 backdrop-blur-md p-4 md:p-6 rounded-xl shadow-2xl w-full max-w-4xl flex flex-col md:flex-row gap-4 items-end"
          >
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4 w-full flex-1">
              <div className="space-y-2 text-left w-full">
                <label className="text-xs font-semibold uppercase tracking-wider text-muted-foreground ml-1">Dates</label>
                <Popover>
                  <PopoverTrigger asChild>
                    <Button
                      variant={"outline"}
                      className={cn(
                        "w-full justify-start text-left font-normal bg-white h-12",
                        !dateRange && "text-muted-foreground"
                      )}
                      data-testid="btn-date-picker"
                    >
                      <CalendarIcon className="mr-2 h-4 w-4 text-primary" />
                      {dateRange?.from ? (
                        dateRange.to ? (
                          <>
                            {format(dateRange.from, "LLL dd, y")} -{" "}
                            {format(dateRange.to, "LLL dd, y")}
                          </>
                        ) : (
                          format(dateRange.from, "LLL dd, y")
                        )
                      ) : (
                        <span>Check in - Check out</span>
                      )}
                    </Button>
                  </PopoverTrigger>
                  <PopoverContent className="w-auto p-0" align="start">
                    <DayPicker
                      mode="range"
                      selected={{
                        from: dateRange.from,
                        to: dateRange.to
                      }}
                      onSelect={(range) => setDateRange({ from: range?.from, to: range?.to })}
                      disabled={{ before: new Date() }}
                      numberOfMonths={2}
                    />
                  </PopoverContent>
                </Popover>
              </div>

              <div className="space-y-2 text-left w-full">
                <label className="text-xs font-semibold uppercase tracking-wider text-muted-foreground ml-1">Guests</label>
                <Select value={guests} onValueChange={setGuests}>
                  <SelectTrigger className="w-full bg-white h-12" data-testid="select-guests">
                    <div className="flex items-center">
                      <Users className="mr-2 h-4 w-4 text-primary" />
                      <SelectValue placeholder="Guests" />
                    </div>
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="1">1 Guest</SelectItem>
                    <SelectItem value="2">2 Guests</SelectItem>
                    <SelectItem value="3">3 Guests</SelectItem>
                    <SelectItem value="4">4 Guests</SelectItem>
                    <SelectItem value="5">5+ Guests</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>

            <Button 
              size="lg" 
              className="h-12 w-full md:w-auto px-8 text-base tracking-wide" 
              onClick={handleSearch}
              data-testid="btn-search-rooms"
            >
              Check Availability
            </Button>
          </motion.div>
        </div>
      </section>

      {/* Featured Rooms */}
      <section className="py-24 bg-card">
        <div className="container mx-auto px-4">
          <div className="flex justify-between items-end mb-12">
            <div>
              <h2 className="text-3xl md:text-4xl font-serif font-bold text-primary mb-4">Featured Accommodations</h2>
              <p className="text-muted-foreground max-w-xl">
                Discover our meticulously designed rooms and suites, tailored for ultimate relaxation.
              </p>
            </div>
            <Button variant="ghost" asChild className="hidden md:flex group">
              <Link href="/rooms">
                View All Rooms <ArrowRight className="ml-2 h-4 w-4 group-hover:translate-x-1 transition-transform" />
              </Link>
            </Button>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {isLoading ? (
              Array(3).fill(0).map((_, i) => (
                <div key={i} className="h-96 bg-muted animate-pulse rounded-xl" />
              ))
            ) : (
              featuredRooms?.slice(0, 3).map((room: any, idx: number) => (
                <motion.div
                  key={room.id}
                  initial={{ opacity: 0, y: 20 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  viewport={{ once: true }}
                  transition={{ delay: idx * 0.1, duration: 0.5 }}
                >
                  <Link href={`/rooms/${room.id}`}>
                    <Card className="h-full overflow-hidden hover:shadow-xl transition-shadow cursor-pointer group border-border/50">
                      <div className="h-64 relative overflow-hidden bg-muted">
                        {room.image_url ? (
                          <img 
                            src={room.image_url} 
                            alt={room.type} 
                            className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                          />
                        ) : (
                          <div className={`w-full h-full flex items-center justify-center font-serif text-2xl text-white/50 capitalize group-hover:scale-105 transition-transform duration-700 ${
                            room.type === 'presidential' ? 'bg-gradient-to-br from-slate-900 to-slate-700' :
                            room.type === 'deluxe' ? 'bg-gradient-to-br from-amber-900 to-amber-700' :
                            room.type === 'suite' ? 'bg-gradient-to-br from-emerald-900 to-emerald-700' :
                            room.type === 'double' ? 'bg-gradient-to-br from-blue-900 to-blue-700' :
                            'bg-gradient-to-br from-stone-600 to-stone-400'
                          }`}>
                            {room.type}
                          </div>
                        )}
                        <div className="absolute top-4 right-4 bg-background/90 backdrop-blur text-primary px-3 py-1 text-sm font-semibold rounded-full">
                          ${room.price_per_night} / night
                        </div>
                      </div>
                      <CardContent className="p-6">
                        <div className="flex justify-between items-start mb-4">
                          <h3 className="text-xl font-serif font-bold capitalize">{room.type} Room</h3>
                          <Badge variant="secondary" className="font-mono">{room.capacity} Guests</Badge>
                        </div>
                        <p className="text-muted-foreground text-sm line-clamp-2 mb-4">
                          {room.description || "A beautiful and comfortable room designed for your perfect stay at Grand Palais."}
                        </p>
                        <div className="flex items-center text-primary font-medium text-sm group-hover:text-accent transition-colors">
                          Explore Room <ArrowRight className="ml-1 h-3 w-3" />
                        </div>
                      </CardContent>
                    </Card>
                  </Link>
                </motion.div>
              ))
            )}
          </div>
          
          <div className="mt-12 text-center md:hidden">
            <Button variant="outline" asChild>
              <Link href="/rooms">View All Rooms</Link>
            </Button>
          </div>
        </div>
      </section>
    </div>
  );
}
