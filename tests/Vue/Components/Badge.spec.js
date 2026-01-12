import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import Badge from '@/Components/ui/badge/Badge.vue';

describe('Badge Component', () => {
    it('renders badge correctly', () => {
        const wrapper = mount(Badge, {
            slots: {
                default: 'Badge Text',
            },
        });

        expect(wrapper.text()).toBe('Badge Text');
    });

    it('renders with default variant', () => {
        const wrapper = mount(Badge, {
            slots: {
                default: 'Default',
            },
        });

        expect(wrapper.exists()).toBe(true);
    });

    it('renders with secondary variant', () => {
        const wrapper = mount(Badge, {
            props: {
                variant: 'secondary',
            },
            slots: {
                default: 'Secondary',
            },
        });

        expect(wrapper.text()).toBe('Secondary');
    });

    it('renders with destructive variant', () => {
        const wrapper = mount(Badge, {
            props: {
                variant: 'destructive',
            },
            slots: {
                default: 'Destructive',
            },
        });

        expect(wrapper.text()).toBe('Destructive');
    });

    it('renders with outline variant', () => {
        const wrapper = mount(Badge, {
            props: {
                variant: 'outline',
            },
            slots: {
                default: 'Outline',
            },
        });

        expect(wrapper.text()).toBe('Outline');
    });

    it('applies custom class', () => {
        const wrapper = mount(Badge, {
            props: {
                class: 'custom-badge',
            },
            slots: {
                default: 'Custom',
            },
        });

        expect(wrapper.classes()).toContain('custom-badge');
    });
});
