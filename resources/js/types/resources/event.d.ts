interface ArtistWithPivot extends Artist {
    pivot: {
        event_id: number;
        artist_id: number;
        performance_time: string | null;
        sort_order: number;
    };
}

interface Event {
    id: number;
    title: string;
    slug: string;
    seo?: import('@/types/seo').Seo;
    start_at: string;
    end_at: string;
    date: string;
    start_time: string;
    end_time: string;
    city: string;
    country: string;
    address: string;
    dress_code: string;
    minimum_age: number;
    description: string;
    body: string;
    background: string;
    poster: string;
    ticketing_starts_at: string | null;
    ticketing_ends_at: string | null;
    ticket_email_content: string | null;
    ticket_pdf_content: string | null;
    stripe_metadata: Record<string, string> | null;
    is_ticketing_open: boolean;
    ticketing_status: 'none' | 'coming_soon' | 'open' | 'closed';
    created_at: string;
    updated_at: string;
    min_price: number;
    faq: Question[];
    genres?: Genre[];
    sponsors?: Sponsor[];
    artists?: ArtistWithPivot[];
    ticket_types?: TicketType[];
    addons?: EventAddon[];
}
