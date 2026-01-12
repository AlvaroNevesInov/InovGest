import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import { Table, TableHeader, TableBody, TableRow, TableHead, TableCell } from '@/Components/ui/table';

describe('Table Component', () => {
    it('renders table correctly', () => {
        const wrapper = mount(Table);

        expect(wrapper.find('table').exists()).toBe(true);
    });

    it('renders table header', () => {
        const wrapper = mount(TableHeader, {
            slots: {
                default: '<tr><th>Header</th></tr>',
            },
        });

        expect(wrapper.find('thead').exists()).toBe(true);
    });

    it('renders table body', () => {
        const wrapper = mount(TableBody, {
            slots: {
                default: '<tr><td>Body</td></tr>',
            },
        });

        expect(wrapper.find('tbody').exists()).toBe(true);
    });

    it('renders table row', () => {
        const wrapper = mount(TableRow, {
            slots: {
                default: '<td>Cell</td>',
            },
        });

        expect(wrapper.find('tr').exists()).toBe(true);
    });

    it('renders table head cell', () => {
        const wrapper = mount(TableHead, {
            slots: {
                default: 'Column Header',
            },
        });

        expect(wrapper.text()).toBe('Column Header');
    });

    it('renders table cell', () => {
        const wrapper = mount(TableCell, {
            slots: {
                default: 'Cell Content',
            },
        });

        expect(wrapper.text()).toBe('Cell Content');
    });

    it('renders complete table structure', () => {
        const wrapper = mount({
            template: `
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Name</TableHead>
                            <TableHead>Email</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow>
                            <TableCell>John Doe</TableCell>
                            <TableCell>john@example.com</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            `,
            components: {
                Table,
                TableHeader,
                TableBody,
                TableRow,
                TableHead,
                TableCell,
            },
        });

        expect(wrapper.find('table').exists()).toBe(true);
        expect(wrapper.find('thead').exists()).toBe(true);
        expect(wrapper.find('tbody').exists()).toBe(true);
        expect(wrapper.findAll('tr').length).toBeGreaterThan(0);
    });
});
