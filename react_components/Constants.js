export const MoneyTabs = [
    {
        link: "/invoices",
        text: "Invoices"
    },
    {
        link: "/estimates",
        text: "Estimates"
    },
    {
        link: "/expenses",
        text: "Expenses"
    },
    {
        link: "/items",
        text: "Products & Services"
    },
    {
        link: "/tinvoices",
        text: "Team Invoices"
    },
    /*
    {
        link: "/leads",
        text: "Leads",
        tag: "NEW"
    }
    */
];
export const WorkTabs = [
    {
        link: "/projects",
        text: "Projects"
    },
    {
        link: "/tickets",
        text: "Tickets"
    },
    /*
    {
        link: "/calendar",
        text: "Calendar",
        isExternal: true
    }
    */
];
export const PeopleTabs = [
    {
        text: "Team",
        link: "/team"
    },
    {
        text: "Clients",
        link: "/clients"
    }
];
export const ShortMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
export const LongMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

export function ProjectTabs(id) {
    return [
        {
            link: `/projects/view/${id}/tasks`,
            text: "Tasks"
        },
        {
            link: `/projects/view/${id}/gantt`,
            text: "Gantt",
            isExternal: true
        },
        {
            link: `/projects/view/${id}/files`,
            text: "Files",
            isExternal: true
        },
        {
            link: `/projects/view/${id}/notes`,
            text: "Notes",
            isExternal: true
        },
        {
            link: `/projects/view/${id}/team`,
            text: "Team"
        },
        {
            link: `/projects/view/${id}/invoices`,
            text: "Invoices",
            isExternal: true
        }
    ];
}