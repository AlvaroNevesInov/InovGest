import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { Button } from '@/Components/ui/button';

describe('Button Component', () => {
    it('renders correctly', () => {
        const wrapper = mount(Button, {
            slots: {
                default: 'Click me',
            },
        });

        expect(wrapper.text()).toContain('Click me');
    });

    it('applies default variant class', () => {
        const wrapper = mount(Button);

        expect(wrapper.classes()).toContain('bg-primary');
    });

    it('applies destructive variant class', () => {
        const wrapper = mount(Button, {
            props: {
                variant: 'destructive',
            },
        });

        expect(wrapper.classes()).toContain('bg-destructive');
    });

    it('applies outline variant class', () => {
        const wrapper = mount(Button, {
            props: {
                variant: 'outline',
            },
        });

        expect(wrapper.classes()).toContain('border');
    });

    it('emits click event', async () => {
        const wrapper = mount(Button);

        await wrapper.trigger('click');

        expect(wrapper.emitted()).toHaveProperty('click');
    });

    it('is disabled when disabled prop is true', () => {
        const wrapper = mount(Button, {
            props: {
                disabled: true,
            },
        });

        expect(wrapper.attributes('disabled')).toBeDefined();
    });

    it('applies small size class', () => {
        const wrapper = mount(Button, {
            props: {
                size: 'sm',
            },
        });

        expect(wrapper.classes()).toContain('h-8');
    });

    it('applies large size class', () => {
        const wrapper = mount(Button, {
            props: {
                size: 'lg',
            },
        });

        expect(wrapper.classes()).toContain('h-10');
    });
});
