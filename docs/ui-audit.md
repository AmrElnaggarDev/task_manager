# UI/UX Audit

This document records the current user-interface problems and the planned design improvements for the Task Manager application.

The goal is to improve consistency, readability, responsiveness, accessibility, and visual hierarchy without weakening the backend-first architecture.

---

## Audit Goals

- Create a consistent visual system
- Improve page hierarchy
- Reduce visual clutter
- Improve mobile responsiveness
- Make actions easier to understand
- Make permissions clearer through the interface
- Improve empty states and feedback
- Prepare the application for portfolio presentation

---

## Global Layout

### Current Concerns

- Page spacing may differ between screens.
- Some pages contain many stacked cards.
- Visual hierarchy is not always clear.
- Main actions and secondary actions may compete for attention.
- Long pages require excessive scrolling.
- Content width may not be optimized for large screens.
- Repeated inline styles make maintenance harder.

### Planned Improvements

- Create shared spacing rules.
- Standardize page headers.
- Standardize content containers.
- Reduce unnecessary cards.
- Use sections instead of cards when appropriate.
- Move repeated inline styles into CSS classes.
- Create reusable Blade components or partials where useful.
- Keep primary actions visually dominant.

---

## Navigation

### Current Concerns

- Navigation hierarchy may be too simple for the number of features.
- Active navigation states should be visually consistent.
- Mobile navigation needs dedicated review.
- Project and task context may not always be obvious.
- Breadcrumbs are limited or missing.

### Planned Improvements

- Add consistent active states.
- Improve sidebar spacing and typography.
- Add breadcrumbs to project and task pages.
- Add a responsive navigation pattern.
- Clearly separate global navigation from project-specific navigation.
- Add icons only where they improve recognition.

---

## Dashboard

### Current Concerns

- Multiple statistics may have equal visual weight.
- Deadline alerts and assigned tasks may compete for attention.
- Cards may become visually repetitive.
- Important actions may not appear near the top.
- Empty sections may use too much space.

### Planned Improvements

- Create a clear top-level dashboard summary.
- Group created tasks and assigned tasks separately.
- Prioritize overdue and due-today tasks.
- Use compact statistic cards.
- Use consistent status colors.
- Improve recent task lists.
- Add better empty states.
- Consider a two-column responsive layout.

---

## Projects Index

### Current Concerns

- Project cards may not provide enough useful information.
- Actions may be visually inconsistent.
- Progress and team information may be missing from the overview.
- Empty state can be improved.

### Planned Improvements

- Display project owner.
- Display member count.
- Display task count.
- Display completion progress.
- Add a consistent project action menu.
- Improve card spacing.
- Add a stronger create-project action.
- Improve the no-projects empty state.

---

## Project Details

### Current Concerns

- Project information, members, statistics, activities, and tasks may create a long page.
- Sections may not have enough visual separation.
- Member management can compete with task information.
- Activity log may take too much vertical space.

### Planned Improvements

- Add a structured project header.
- Display owner and member summary near the title.
- Place project statistics in a compact summary row.
- Use a dedicated tasks section.
- Make member management collapsible or modal-based.
- Improve activity log density.
- Consider tabs only when the information hierarchy is finalized.

Possible tabs:

- Overview
- Tasks
- Members
- Activity

---

## Tasks Index

### Current Concerns

- Filters and sorting can make the top of the page crowded.
- Too many controls may reduce clarity.
- Task rows or cards may display too much information.
- Mobile layouts need review.
- Active filters may not be obvious.

### Planned Improvements

- Group search, filters, and sorting.
- Use a responsive filter toolbar.
- Show active filter badges.
- Add a clear reset action.
- Standardize task status and priority badges.
- Improve deadline presentation.
- Improve assignee presentation.
- Consider pagination before the list becomes large.
- Make task rows easy to scan.

---

## Task Details

### Current Concerns

This is currently the most complex page.

The page contains:

- Task metadata
- Task description
- Task actions
- Status update
- Checklist
- Attachments
- Comments

This can create excessive scrolling and weak hierarchy.

### Planned Improvements

- Create a clear task header.
- Place status, priority, deadline, project, creator, and assignee in a compact metadata section.
- Separate primary task actions from destructive actions.
- Move status update near the task status.
- Group checklist, attachments, and comments into structured sections.
- Improve checklist density.
- Improve attachment row layout.
- Improve comments readability.
- Consider tabs after redesign.

Possible layout:

### Left Column

- Task description
- Checklist
- Attachments
- Comments

### Right Column

- Status
- Priority
- Deadline
- Assignee
- Creator
- Project
- Main actions

Possible tabs:

- Overview
- Checklist
- Files
- Discussion

---

## Forms

### Current Concerns

- Validation messages may not be presented consistently.
- Form spacing may vary.
- Required fields may not be clearly marked.
- Button ordering may differ between pages.
- Large forms may not be grouped logically.

### Planned Improvements

- Standardize labels.
- Standardize help text.
- Standardize validation messages.
- Mark required fields consistently.
- Group related inputs.
- Keep primary submit action first.
- Keep cancel action secondary.
- Keep delete actions separate from normal forms.
- Add consistent input widths.
- Improve mobile form spacing.

---

## Buttons

### Current Concerns

- Button colors may not always represent action priority.
- Icon-only buttons need accessible labels.
- Edit and delete actions may appear too prominent.
- Different pages may use different button sizes.

### Planned Improvements

- Primary actions use `btn-primary`.
- Secondary actions use `btn-outline-secondary`.
- Destructive actions use `btn-danger` or `btn-outline-danger`.
- Small table actions use a consistent size.
- Icon-only buttons include `title` and `aria-label`.
- Delete actions require confirmation.
- Avoid using warning color for normal editing unless there is a semantic reason.

---

## Cards

### Current Concerns

- Too many cards can create visual noise.
- Shadows and borders may be inconsistent.
- Nested cards reduce clarity.
- Card headers may not follow a shared pattern.

### Planned Improvements

- Use cards only for grouped content.
- Avoid cards inside cards.
- Standardize border radius.
- Standardize shadows.
- Standardize card padding.
- Use section headings for simple content.
- Create one reusable card header pattern.

---

## Badges and Status Colors

### Planned Semantic Rules

#### Task Status

- To Do: secondary
- In Progress: primary or info
- Done: success

#### Priority

- Low: secondary
- Medium: warning
- High: danger

#### Deadlines

- Overdue: danger
- Due Today: warning
- Upcoming: info
- No Deadline: secondary

#### Activity

Activity badges keep action-specific colors.

### Current Concerns

- Colors should not be the only way to communicate state.
- Text labels and icons should remain visible.
- Badge colors must remain consistent across all pages.

---

## Responsive Design

### Current Concerns

- Wide filter forms may wrap badly.
- Action buttons may become crowded.
- Tables may overflow.
- Task metadata may become difficult to scan.
- Sidebar behavior requires review.
- Long filenames may break layouts.

### Planned Improvements

- Test at mobile, tablet, and desktop widths.
- Stack filter controls on small screens.
- Use responsive grid classes.
- Add `text-truncate` where appropriate.
- Use responsive tables only when tables are necessary.
- Stack task detail columns on mobile.
- Make button groups wrap safely.
- Ensure forms remain usable with touch input.

---

## Accessibility

### Current Concerns

- Some icon-only buttons may rely on title attributes only.
- Form controls need consistent labels.
- Color contrast requires review.
- Checkbox actions need clear labels.
- Focus states should remain visible.
- Confirmation dialogs currently rely on browser confirm.

### Planned Improvements

- Add `aria-label` to icon-only actions.
- Use visible labels where possible.
- Preserve keyboard focus styles.
- Improve color contrast.
- Do not communicate status through color alone.
- Add accessible text for checklist toggles.
- Add descriptive alternative text to images.
- Use semantic headings in correct order.
- Review modal keyboard behavior.

---

## Empty States

### Current Concerns

- Some empty states are simple text only.
- Users may not know what action to take next.

### Planned Improvements

Each empty state should include:

- A clear explanation
- A relevant icon
- A primary next action when authorized
- A short helpful description

Examples:

- No projects: create your first project.
- No tasks: create the first project task.
- No comments: start the discussion.
- No attachments: upload a supporting file.
- No checklist items: break the task into smaller steps.
- No activity: project actions will appear here.

---

## Feedback Messages

### Planned Improvements

- Standardize success messages.
- Standardize validation errors.
- Standardize unauthorized behavior.
- Use dismissible Bootstrap alerts.
- Keep messages short and action-specific.
- Consider toast notifications during the UI redesign.

---

## Visual Consistency

### Planned Design Tokens

The redesign should define:

- Primary color
- Secondary text color
- Border color
- Background color
- Card radius
- Input radius
- Button radius
- Small spacing
- Medium spacing
- Large spacing
- Heading sizes
- Body text size
- Muted text size

### Planned Shared Components

Potential reusable Blade components:

- Page header
- Section header
- Empty state
- Status badge
- Priority badge
- Deadline badge
- Flash alert
- Confirmation modal
- User avatar
- Statistic card

---

## Recommended Redesign Order

1. Global layout and navigation
2. Shared colors and spacing
3. Reusable UI components
4. Dashboard
5. Projects index
6. Project details
7. Tasks index
8. Task details
9. Forms
10. Mobile responsiveness
11. Accessibility review
12. Updated screenshots

---

## Features That Affect the Design Roadmap

Future features should be considered while redesigning:

- Notifications
- Project invitations
- Project roles
- Comment editing
- Pagination
- REST API management
- User preferences
- Activity history filters

The design should leave space for notification indicators, role labels, pagination controls, and future project navigation.

---

## Final UI Goal

The final interface should feel:

- Clean
- Consistent
- Professional
- Easy to scan
- Responsive
- Accessible
- Suitable for a portfolio demonstration

The redesign should improve presentation without hiding the application's backend complexity.
