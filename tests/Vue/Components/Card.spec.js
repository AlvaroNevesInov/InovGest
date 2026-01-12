import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/Components/ui/card';

describe('Card Component', () => {
    it('renders card correctly', () => {
        const wrapper = mount(Card);

        expect(wrapper.exists()).toBe(true);
    });

    it('renders card with content', () => {
        const wrapper = mount(Card, {
            slots: {
                default: 'Card content',
            },
        });

        expect(wrapper.text()).toBe('Card content');
    });

    it('renders card header', () => {
        const wrapper = mount(CardHeader, {
            slots: {
                default: 'Header',
            },
        });

        expect(wrapper.text()).toBe('Header');
    });

    it('renders card title', () => {
        const wrapper = mount(CardTitle, {
            slots: {
                default: 'Card Title',
            },
        });

        expect(wrapper.text()).toBe('Card Title');
    });

    it('renders card description', () => {
        const wrapper = mount(CardDescription, {
            slots: {
                default: 'Card description text',
            },
        });

        expect(wrapper.text()).toBe('Card description text');
    });

    it('renders card content', () => {
        const wrapper = mount(CardContent, {
            slots: {
                default: 'Main content',
            },
        });

        expect(wrapper.text()).toBe('Main content');
    });

    it('renders card footer', () => {
        const wrapper = mount(CardFooter, {
            slots: {
                default: 'Footer content',
            },
        });

        expect(wrapper.text()).toBe('Footer content');
    });

    it('applies custom class to card', () => {
        const wrapper = mount(Card, {
            props: {
                class: 'custom-card',
            },
        });

        expect(wrapper.classes()).toContain('custom-card');
    });

    it('renders complete card structure', () => {
        const wrapper = mount({
            template: `
                <Card>
                    <CardHeader>
                        <CardTitle>Title</CardTitle>
                        <CardDescription>Description</CardDescription>
                    </CardHeader>
                    <CardContent>Content</CardContent>
                    <CardFooter>Footer</CardFooter>
                </Card>
            `,
            components: {
                Card,
                CardHeader,
                CardTitle,
                CardDescription,
                CardContent,
                CardFooter,
            },
        });

        expect(wrapper.text()).toContain('Title');
        expect(wrapper.text()).toContain('Description');
        expect(wrapper.text()).toContain('Content');
        expect(wrapper.text()).toContain('Footer');
    });
});
