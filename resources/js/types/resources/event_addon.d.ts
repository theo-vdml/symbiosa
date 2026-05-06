interface EventAddon {
    id: number;
    event_id: number;
    name: string;
    description: string;
    price: number;
    price_in_euro: number;
    max_per_order: number;
    sort_order: number;
    available_from: string;
    available_until: string;
    capacity: number | null;
    sold_count: number;
    status: 'open' | 'upcoming' | 'sold_out';
}
