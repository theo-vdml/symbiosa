interface TicketType {
    id: number;
    event_id: number;
    name: string;
    description: string;
    max_per_order?: number | null;
    sort_order: number;
    available_from: string;
    capacity: number | null;
    sold_count: number;
    prices: TicketPrice[];
}

interface TicketPrice {
    id: number;
    ticket_type_id: number;
    name: string;
    price: number;
    price_in_euro: number;
    available_until: string;
    threshold: number | null;
    sort_order: number;
    status: 'available' | 'soon' | 'sold_out';
}
