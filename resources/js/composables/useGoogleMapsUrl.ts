import type { MaybeRefOrGetter } from 'vue';
import { computed, toValue } from 'vue';

export function useGoogleMapsUrl(
    address: MaybeRefOrGetter<string | undefined | null>,
) {
    return computed(() => {
        const val = toValue(address);

        if (!val) {
            return '#';
        }

        const enccodedAddress = encodeURIComponent(val);

        return `https://www.google.com/maps/search/?api=1&query=${enccodedAddress}`;
    });
}
