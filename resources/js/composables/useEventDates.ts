import { computed, toValue, type MaybeRefOrGetter } from 'vue';

const dateTimeFormatLocale = 'fr-FR';

const longFormatter = new Intl.DateTimeFormat(dateTimeFormatLocale, {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
});

const weekdayFormatter = new Intl.DateTimeFormat(dateTimeFormatLocale, {
    weekday: 'long',
});

const dayFormatter = new Intl.DateTimeFormat(dateTimeFormatLocale, {
    day: 'numeric',
});

const monthFormatter = new Intl.DateTimeFormat(dateTimeFormatLocale, {
    month: 'short',
});

const timeFormatter = new Intl.DateTimeFormat(dateTimeFormatLocale, {
    hour: '2-digit',
    minute: '2-digit',
});

interface EventWithDates {
    start_at: string | Date;
    end_at: string | Date;
}

export function useEventDates(
    eventParam: MaybeRefOrGetter<EventWithDates | undefined | null>,
) {
    const getSafeValue = (key: keyof EventWithDates) => {
        const event = toValue(eventParam);
        if (!event || !event[key]) return null;
        return new Date(event[key]);
    };

    const startLong = computed(() => {
        const date = getSafeValue('start_at');
        return date ? longFormatter.format(date) : '';
    });

    const startWeekday = computed(() => {
        const date = getSafeValue('start_at');
        return date ? weekdayFormatter.format(date) : '';
    });

    const startDay = computed(() => {
        const date = getSafeValue('start_at');
        return date ? dayFormatter.format(date) : '';
    });

    const startMonth = computed(() => {
        const date = getSafeValue('start_at');
        return date ? monthFormatter.format(date) : '';
    });

    const startTime = computed(() => {
        const date = getSafeValue('start_at');
        return date ? timeFormatter.format(date) : '';
    });

    const endLong = computed(() => {
        const date = getSafeValue('end_at');
        return date ? longFormatter.format(date) : '';
    });

    const endWeekday = computed(() => {
        const date = getSafeValue('end_at');
        return date ? weekdayFormatter.format(date) : '';
    });

    const endDay = computed(() => {
        const date = getSafeValue('end_at');
        return date ? dayFormatter.format(date) : '';
    });

    const endMonth = computed(() => {
        const date = getSafeValue('end_at');
        return date ? monthFormatter.format(date) : '';
    });

    const endTime = computed(() => {
        const date = getSafeValue('end_at');
        return date ? timeFormatter.format(date) : '';
    });

    return {
        startLong,
        startWeekday,
        startDay,
        startMonth,
        startTime,
        endLong,
        endWeekday,
        endDay,
        endMonth,
        endTime,
    };
}
