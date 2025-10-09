import { route } from 'ziggy-js'

export function useSafeRoute() {
    const safeRoute = (...args) => {
        try {
            const routeName = args[0]
            return route().has(routeName) ? route(...args) : null
        } catch (e) {
            return null
        }
    }
    return { safeRoute }
}
