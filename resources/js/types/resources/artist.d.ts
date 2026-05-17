interface Artist {
    id: number;
    name: string;
    portrait_url: string;
    website: string;
    genres?: Genre[];
    biography: string;
}
