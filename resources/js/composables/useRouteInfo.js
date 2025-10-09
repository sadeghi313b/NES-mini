import { usePage } from '@inertiajs/vue3';

export function useRouteInfo() {
    const page = usePage();
    const routeName = String(page.props.theRoute.name);
    const routeParts = [...page.props.theRoute.parts];
    const lastRoutePart = routeParts.at(-1);

    let baseRouteWithDot;
    let routeMethod;

    if (['edit', 'show', 'create', 'index', 'delete'].includes(lastRoutePart)) {
        routeMethod = routeParts.pop();
        baseRouteWithDot = routeParts.join('.') + '.';
    } else {
        baseRouteWithDot = page.props.theRoute.name;
        routeMethod = null;
    }

    return {
        routeMethod,
        baseRouteWithDot,
        routeName,
    };
}