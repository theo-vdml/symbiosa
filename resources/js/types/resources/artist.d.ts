interface Artist {
    id: number;
    name: string;
    thumbnail: string;
    website: string;
    genres?: Genre[];
    biography: string;
}
