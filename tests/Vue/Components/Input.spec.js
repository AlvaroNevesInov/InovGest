import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { Input } from '@/Components/ui/input';

describe('Input Component', () => {
    it('renders correctly', () => {
        const wrapper = mount(Input);

        expect(wrapper.find('input').exists()).toBe(true);
    });

    it('binds v-model correctly', async () => {
        const wrapper = mount(Input, {
            props: {
                modelValue: 'test value',
                'onUpdate:modelValue': (e) => wrapper.setProps({ modelValue: e }),
            },
        });

        expect(wrapper.find('input').element.value).toBe('test value');

        await wrapper.find('input').setValue('new value');

        expect(wrapper.emitted('update:modelValue')).toBeTruthy();
        expect(wrapper.emitted('update:modelValue')[0]).toEqual(['new value']);
    });

    it('applies disabled attribute', () => {
        const wrapper = mount(Input, {
            props: {
                disabled: true,
            },
        });

        expect(wrapper.find('input').attributes('disabled')).toBeDefined();
    });

    it('applies type attribute', () => {
        const wrapper = mount(Input, {
            props: {
                type: 'email',
            },
        });

        expect(wrapper.find('input').attributes('type')).toBe('email');
    });

    it('applies placeholder attribute', () => {
        const wrapper = mount(Input, {
            props: {
                placeholder: 'Enter text',
            },
        });

        expect(wrapper.find('input').attributes('placeholder')).toBe('Enter text');
    });

    it('applies custom class', () => {
        const wrapper = mount(Input, {
            props: {
                class: 'custom-class',
            },
        });

        expect(wrapper.classes()).toContain('custom-class');
    });
});
