# Grand Palais Hotel Booking — Frontend

A beautiful, fully functional React + Vite hotel booking frontend for the Grand Palais Hotel.

## Tech Stack

- React 19 + Vite 7
- TypeScript
- Tailwind CSS v4
- shadcn/ui components
- TanStack Query (data fetching)
- Wouter (routing)
- Framer Motion (animations)
- React Hook Form + Zod (form validation)

## Pages

| Route | Description |
|-------|-------------|
| `/` | Landing page with hero and room search |
| `/rooms` | Room listing with filters |
| `/rooms/:id` | Room detail with booking form |
| `/login` | Sign in page |
| `/register` | Create account page |
| `/dashboard` | My bookings (auth required) |

## Setup

```bash
# Install dependencies (requires pnpm)
pnpm install

# Start dev server
pnpm dev

# Build for production
pnpm build
```

## Environment

The app connects directly to the backend API hosted on Render:
`https://hotel-booking-api-1-zmcs.onrender.com`

No environment variables required — the API URL is configured in `src/lib/api.ts`.

## Features

- Browse all available rooms with real-time data from the API
- Filter rooms by type, guest capacity, and price
- View detailed room information with amenities
- Date picker for booking with automatic price calculation
- JWT authentication (stored in localStorage)
- View and cancel your bookings from the dashboard
