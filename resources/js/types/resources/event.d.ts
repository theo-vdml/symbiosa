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
    date: string;
    start_time: string;
    end_time: string;
    city: string;
    country: string;
    address: string;
    dress_code: string;
    minimum_age: number;
    description: string;
    background: string;
    poster: string;
    created_at: string;
    updated_at: string;
    faq: Question[];
    genres?: Genre[];
    sponsors?: Sponsor[];
    artists?: ArtistWithPivot[];
    ticket_types?: TicketType[];
    addons?: EventAddon[];
}
