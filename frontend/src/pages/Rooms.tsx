import { useState, useMemo } from "react";
import { useQuery } from "@tanstack/react-query";
import { Link, useLocation } from "wouter";
import { motion } from "framer-motion";
import { Filter, SlidersHorizontal, Users, BedDouble } from "lucide-react";

import { api } from "@/lib/api";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Skeleton } from "@/components/ui/skeleton";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Slider } from "@/components/ui/slider";

export default function Rooms() {
  const [location] = useLocation();
  const searchParams = new URLSearchParams(window.location.search);
  
  const initialCapacity = searchParams.get("capacity") || "all";
  
  const [typeFilter, setTypeFilter] = useState("all");
  const [capacityFilter, setCapacityFilter] = useState(initialCapacity);
  const [priceRange, setPriceRange] = useState([1000]); // Max price

  const { data: rooms, isLoading } = useQuery({
    queryKey: ["rooms"],
    queryFn: () => api.getRooms(),
  });

  const filteredRooms = useMemo(() => {
    if (!rooms) return [];
    return rooms.filter((room: any) => {
      const matchType = typeFilter === "all" || room.type === typeFilter;
      const matchCapacity = capacityFilter === "all" || room.capacity >= parseInt(capacityFilter);
      const matchPrice = parseFloat(room.price_per_night) <= priceRange[0];
      return matchType && matchCapacity && matchPrice;
    });
  }, [rooms, typeFilter, capacityFilter, priceRange]);

  return (
    <div className="container mx-auto px-4 py-12">
      <div className="mb-12 text-center max-w-2xl mx-auto">
        <h1 className="text-4xl md:text-5xl font-serif font-bold text-primary mb-4">Our Accommodations</h1>
        <p className="text-muted-foreground">
          Find your perfect sanctuary. Each room is designed with meticulous attention to detail and your absolute comfort in mind.
        </p>
      </div>

      <div className="flex flex-col lg:flex-row gap-8">
        {/* Filters Sidebar */}
        <div className="w-full lg:w-64 shrink-0 space-y-8">
          <div className="bg-card p-6 rounded-xl border border-border sticky top-24">
            <div className="flex items-center gap-2 mb-6 text-primary font-serif font-bold text-xl">
              <SlidersHorizontal className="h-5 w-5" />
              <span>Filters</span>
            </div>

            <div className="space-y-6">
              <div className="space-y-3">
                <label className="text-sm font-semibold uppercase tracking-wider text-muted-foreground">Room Type</label>
                <Select value={typeFilter} onValueChange={setTypeFilter}>
                  <SelectTrigger data-testid="filter-type">
                    <SelectValue placeholder="All Types" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all">All Types</SelectItem>
                    <SelectItem value="single">Single</SelectItem>
                    <SelectItem value="double">Double</SelectItem>
                    <SelectItem value="suite">Suite</SelectItem>
                    <SelectItem value="deluxe">Deluxe</SelectItem>
                    <SelectItem value="presidential">Presidential</SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <div className="space-y-3">
                <label className="text-sm font-semibold uppercase tracking-wider text-muted-foreground">Guests</label>
                <Select value={capacityFilter} onValueChange={setCapacityFilter}>
                  <SelectTrigger data-testid="filter-capacity">
                    <SelectValue placeholder="Any Capacity" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all">Any Capacity</SelectItem>
                    <SelectItem value="1">1+ Guests</SelectItem>
                    <SelectItem value="2">2+ Guests</SelectItem>
                    <SelectItem value="3">3+ Guests</SelectItem>
                    <SelectItem value="4">4+ Guests</SelectItem>
                    <SelectItem value="6">6+ Guests</SelectItem>
                  </SelectContent>
                </Select>
              </div>

              <div className="space-y-4">
                <div className="flex justify-between">
                  <label className="text-sm font-semibold uppercase tracking-wider text-muted-foreground">Max Price</label>
                  <span className="text-sm font-mono text-primary">${priceRange[0]}</span>
                </div>
                <Slider
                  defaultValue={[1000]}
                  max={1500}
                  step={50}
                  value={priceRange}
                  onValueChange={setPriceRange}
                  data-testid="filter-price"
                />
              </div>

              <Button 
                variant="outline" 
                className="w-full mt-4"
                onClick={() => {
                  setTypeFilter("all");
                  setCapacityFilter("all");
                  setPriceRange([1000]);
                }}
              >
                Reset Filters
              </Button>
            </div>
          </div>
        </div>

        {/* Room Grid */}
        <div className="flex-1">
          <div className="mb-6 flex justify-between items-center">
            <h2 className="text-xl font-medium">
              {isLoading ? "Loading..." : `${filteredRooms.length} Rooms Available`}
            </h2>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {isLoading ? (
              Array(6).fill(0).map((_, i) => (
                <div key={i} className="flex flex-col space-y-3">
                  <Skeleton className="h-64 rounded-xl" />
                  <Skeleton className="h-6 w-2/3" />
                  <Skeleton className="h-4 w-1/2" />
                  <Skeleton className="h-10 w-full" />
                </div>
              ))
            ) : filteredRooms.length === 0 ? (
              <div className="col-span-2 py-20 text-center bg-muted/30 rounded-xl border border-dashed border-border">
                <BedDouble className="h-12 w-12 mx-auto text-muted-foreground mb-4 opacity-50" />
                <h3 className="text-xl font-serif text-primary mb-2">No rooms found</h3>
                <p className="text-muted-foreground">Try adjusting your filters to see more results.</p>
              </div>
            ) : (
              filteredRooms.map((room: any, idx: number) => (
                <motion.div
                  key={room.id}
                  initial={{ opacity: 0, y: 10 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: idx * 0.05 }}
                  className="h-full"
                >
                  <Card className="h-full flex flex-col overflow-hidden group hover:shadow-lg transition-all border-border/60">
                    <div className="h-56 relative overflow-hidden bg-muted">
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
                      <div className="absolute top-4 right-4 bg-background/90 backdrop-blur text-primary px-3 py-1 rounded-full text-sm font-bold shadow-sm">
                        ${room.price_per_night} <span className="text-xs font-normal text-muted-foreground">/ night</span>
                      </div>
                    </div>
                    <CardContent className="p-6 flex-1 flex flex-col">
                      <div className="flex justify-between items-start mb-3">
                        <h3 className="text-2xl font-serif font-bold capitalize text-primary">{room.type}</h3>
                        <Badge variant="outline" className="flex items-center gap-1 font-mono">
                          <Users className="h-3 w-3" /> {room.capacity}
                        </Badge>
                      </div>
                      
                      <p className="text-muted-foreground text-sm line-clamp-2 mb-6 flex-1">
                        {room.description || `Experience ultimate comfort in our ${room.type} room, perfectly appointed for your stay.`}
                      </p>
                      
                      <div className="flex flex-wrap gap-2 mb-6">
                        {(room.amenities ? room.amenities.split(',').slice(0, 3) : ['Wi-Fi', 'Room Service', 'Mini Bar']).map((amenity: string, i: number) => (
                          <Badge key={i} variant="secondary" className="text-xs bg-muted/50 text-muted-foreground">
                            {amenity.trim()}
                          </Badge>
                        ))}
                        {(room.amenities?.split(',').length > 3) && (
                          <Badge variant="secondary" className="text-xs bg-muted/50 text-muted-foreground">
                            +{room.amenities.split(',').length - 3} more
                          </Badge>
                        )}
                      </div>

                      <Button asChild className="w-full h-12" data-testid={`btn-book-${room.id}`}>
                        <Link href={`/rooms/${room.id}`}>Book Now</Link>
                      </Button>
                    </CardContent>
                  </Card>
                </motion.div>
              ))
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
