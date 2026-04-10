import { ReactNode } from "react";
import { Link, useLocation } from "wouter";
import { useAuth } from "@/context/AuthContext";
import { Button } from "@/components/ui/button";

export function Layout({ children }: { children: ReactNode }) {
  const { user, isAuthenticated, logout } = useAuth();
  const [location, setLocation] = useLocation();

  const handleLogout = async () => {
    await logout();
    setLocation("/");
  };

  return (
    <div className="min-h-screen flex flex-col bg-background font-sans text-foreground">
      <header className="sticky top-0 z-50 w-full border-b border-border/40 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
        <div className="container mx-auto px-4 h-20 flex items-center justify-between">
          <Link href="/" className="flex items-center gap-2">
            <span className="font-serif text-2xl font-bold tracking-tight text-primary">Grand Palais</span>
          </Link>
          
          <nav className="hidden md:flex items-center gap-8">
            <Link href="/rooms" className="text-sm font-medium hover:text-accent transition-colors" data-testid="nav-rooms">
              Rooms & Suites
            </Link>
            {isAuthenticated ? (
              <>
                <Link href="/dashboard" className="text-sm font-medium hover:text-accent transition-colors" data-testid="nav-dashboard">
                  My Bookings
                </Link>
                <div className="flex items-center gap-4 ml-4">
                  <span className="text-sm text-muted-foreground">Welcome, {user?.name}</span>
                  <Button variant="outline" size="sm" onClick={handleLogout} data-testid="nav-logout">
                    Logout
                  </Button>
                </div>
              </>
            ) : (
              <div className="flex items-center gap-4 ml-4">
                <Button variant="ghost" size="sm" asChild>
                  <Link href="/login" data-testid="nav-login">Login</Link>
                </Button>
                <Button size="sm" asChild>
                  <Link href="/register" data-testid="nav-register">Register</Link>
                </Button>
              </div>
            )}
          </nav>
        </div>
      </header>

      <main className="flex-1">
        {children}
      </main>

      <footer className="border-t border-border bg-card mt-auto">
        <div className="container mx-auto px-4 py-12 flex flex-col md:flex-row justify-between items-center gap-6">
          <div>
            <span className="font-serif text-xl font-bold text-primary block mb-2">Grand Palais</span>
            <p className="text-sm text-muted-foreground max-w-xs">
              A boutique luxury experience. Unwind, relax, and enjoy the perfect stay.
            </p>
          </div>
          <div className="text-sm text-muted-foreground text-center md:text-right">
            <p>&copy; {new Date().getFullYear()} Grand Palais Hotel.</p>
            <p className="mt-1">All rights reserved.</p>
          </div>
        </div>
      </footer>
    </div>
  );
}
