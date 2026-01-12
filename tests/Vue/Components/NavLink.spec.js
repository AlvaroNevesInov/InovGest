import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import NavLink from '@/Components/NavLink.vue';
import { Link } from '@inertiajs/vue3';

// Mock Inertia Link component
vi.mock('@inertiajs/vue3', () => ({
    Link: {
        name: 'Link',
        template: '<a :href="href" :class="$attrs.class"><slot /></a>',
        props: ['href'],
    },
}));

describe('NavLink Component', () => {
    it('renders correctly', () => {
        const wrapper = mount(NavLink, {
            props: {
                href: '/dashboard',
            },
            slots: {
                default: 'Dashboard',
            },
            global: {
                components: {
                    Link,
                },
            },
        });

        expect(wrapper.text()).toContain('Dashboard');
        expect(wrapper.find('a').attributes('href')).toBe('/dashboard');
    });

    it('applies active classes when active prop is true', () => {
        const wrapper = mount(NavLink, {
            props: {
                href: '/dashboard',
                active: true,
            },
            global: {
                components: {
                    Link,
                },
            },
        });

        expect(wrapper.find('a').classes()).toContain('border-indigo-400');
        expect(wrapper.find('a').classes()).toContain('text-gray-900');
    });

    it('applies inactive classes when active prop is false', () => {
        const wrapper = mount(NavLink, {
            props: {
                href: '/dashboard',
                active: false,
            },
            global: {
                components: {
                    Link,
                },
            },
        });

        expect(wrapper.find('a').classes()).toContain('border-transparent');
        expect(wrapper.find('a').classes()).toContain('text-gray-500');
    });

    it('has h-16 class for vertical alignment', () => {
        const wrapper = mount(NavLink, {
            props: {
                href: '/dashboard',
            },
            global: {
                components: {
                    Link,
                },
            },
        });

        expect(wrapper.find('a').classes()).toContain('h-16');
    });

    it('has inline-flex and items-center classes', () => {
        const wrapper = mount(NavLink, {
            props: {
                href: '/dashboard',
            },
            global: {
                components: {
                    Link,
                },
            },
        });

        expect(wrapper.find('a').classes()).toContain('inline-flex');
        expect(wrapper.find('a').classes()).toContain('items-center');
    });
});
