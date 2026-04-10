export const API_BASE_URL = 'https://hotel-booking-api-1-zmcs.onrender.com';

const TOKEN_KEY = 'hotel_token';

export const getToken = (): string | null => {
  return localStorage.getItem(TOKEN_KEY);
};

export const setToken = (token: string): void => {
  localStorage.setItem(TOKEN_KEY, token);
};

export const clearToken = (): void => {
  localStorage.removeItem(TOKEN_KEY);
};

export const authFetch = async (path: string, options: RequestInit = {}): Promise<Response> => {
  const token = getToken();
  const headers = new Headers(options.headers || {});

  if (token) {
    headers.set('Authorization', `Bearer ${token}`);
  }

  headers.set('Content-Type', 'application/json');

  const response = await fetch(`${API_BASE_URL}${path}`, {
    ...options,
    headers,
  });

  if (response.status === 401) {
    clearToken();
  }

  return response;
};

export interface Room {
  id: number;
  room_number: string;
  type: string;
  floor: number;
  capacity: number;
  price_per_night: string;
  description: string;
  amenities: string;
  image_url: string | null;
  status: string;
  created_at: string;
  updated_at: string;
}

export interface User {
  id: number;
  name: string;
  email: string;
  phone: string | null;
  role: string;
  status: string;
  created_at: string;
  updated_at: string;
}

export interface Booking {
  id: number;
  booking_reference: string;
  user_id: number;
  room_id: number;
  check_in: string;
  check_out: string;
  nights: number;
  guests: number;
  total_price: string;
  status: 'pending' | 'confirmed' | 'cancelled';
  special_requests: string;
  notes: string | null;
  created_at: string;
  updated_at: string;
  guest_name: string;
  guest_email: string;
  room_number: string;
  room_type: string;
  price_per_night: string;
}

export const api = {
  getRooms: async (params?: Record<string, string>): Promise<Room[]> => {
    const query = params ? `?${new URLSearchParams(params).toString()}` : '';
    const res = await fetch(`${API_BASE_URL}/api/rooms${query}`);
    if (!res.ok) throw new Error('Failed to fetch rooms');
    const json = await res.json();
    return json.data?.rooms || [];
  },

  getRoom: async (id: string | number): Promise<Room> => {
    const res = await fetch(`${API_BASE_URL}/api/rooms/${id}`);
    if (!res.ok) throw new Error('Failed to fetch room');
    const json = await res.json();
    return json.data;
  },

  login: async (email: string, password: string): Promise<{ token: string; user: User }> => {
    const res = await fetch(`${API_BASE_URL}/api/auth/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, password }),
    });
    const json = await res.json();
    if (!res.ok) throw new Error(json.message || 'Login failed');
    return json.data;
  },

  register: async (name: string, email: string, password: string): Promise<{ token: string; user: User }> => {
    const res = await fetch(`${API_BASE_URL}/api/auth/register`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name, email, password }),
    });
    const json = await res.json();
    if (!res.ok) throw new Error(json.message || 'Registration failed');
    return json.data;
  },

  logout: async (): Promise<void> => {
    try {
      await authFetch('/api/auth/logout', { method: 'POST' });
    } catch (_) {
      // ignore
    } finally {
      clearToken();
    }
  },

  getMe: async (): Promise<User> => {
    const res = await authFetch('/api/auth/me');
    if (!res.ok) throw new Error('Failed to get user');
    const json = await res.json();
    return json.data;
  },

  getMyBookings: async (): Promise<Booking[]> => {
    const res = await authFetch('/api/bookings');
    if (!res.ok) throw new Error('Failed to fetch bookings');
    const json = await res.json();
    return json.data?.bookings || [];
  },

  createBooking: async (data: {
    room_id: number;
    check_in: string;
    check_out: string;
    guests: number;
    special_requests?: string;
  }): Promise<Booking> => {
    const res = await authFetch('/api/bookings', {
      method: 'POST',
      body: JSON.stringify(data),
    });
    const json = await res.json();
    if (!res.ok) throw new Error(json.message || 'Failed to create booking');
    return json.data;
  },

  cancelBooking: async (id: number): Promise<void> => {
    const res = await authFetch(`/api/bookings/${id}/cancel`, {
      method: 'POST',
    });
    const json = await res.json();
    if (!res.ok) throw new Error(json.message || 'Failed to cancel booking');
  }
};
