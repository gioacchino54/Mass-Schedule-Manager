# Mass Schedule Manager (com_messe + mod_messe)

*Package version: 1.3.23 (com_messe 1.3.22 · mod_messe 1.1.11)*

## Short description (for the JED "Short Description" field)

A Joomla component to manage and publish Mass schedules for one or more churches, with configurable vigil (anticipated) Masses, holiday exceptions, special periods (e.g. summer schedules), and a companion module to display them anywhere on the site.

## Full description

**Mass Schedule Manager** (*Gestione Orari Messe*) is a component for Joomla 5 and 6 designed for parishes, dioceses and religious community websites that need a simple, always up-to-date way to publish Mass schedules for one or more churches.

From the administrator panel you can:

- manage an unlimited number of churches, each with name, description, address and rite (Roman or Ambrosian);
- set up weekly schedules split into **weekday**, **vigil** (Saturday evening) and **holiday**, with the option to restrict a single time slot to specific days of the week (e.g. Tuesday and Thursday only), automatically shown by name on the frontend;
- flexibly configure, per church, **how the anticipated ("vigil") Mass for a midweek feast** is handled: none, same time as the Saturday vigil Mass, a specific dedicated schedule, or automatic calculation from the last evening weekday Mass (with an optional min/max time range);
- choose whether **active seasonal periods** (e.g. summer schedule) should also determine the vigil Mass time, or whether it should always ignore them;
- manage **date-specific exceptions** (e.g. a special schedule for a patronal feast);
- manage **periods** (date ranges or whole months, e.g. July–August) during which a schedule type can be suppressed or replaced with an alternative one (useful for summer schedules);
- display, for each church on the frontend, the next upcoming Mass and the full list of scheduled times, with the option to also show the weekday in the vigil Masses list;
- use a built-in test mode with a simulated date, useful to preview how the schedule will be displayed on a future date (e.g. Christmas) without having to wait for it;
- choose whether to **keep data in the database** when the component is uninstalled, so configuration isn't lost during an update.

The package also includes **mod_messe**, a frontend module that displays the next Mass time (or the full schedule) for a specific church in any module position of your template — ideal for the homepage or a sidebar.

## Key features

- Multi-church management with Roman/Ambrosian rite support
- Weekday, vigil and holiday schedules, with optional restriction to specific weekdays (shown by name on the frontend, e.g. "Tuesday, Thursday")
- **Configurable vigil/anticipated Mass per church**, with 4 modes (none / same time as Saturday vigil / dedicated schedule / automatic from last evening weekday Mass with optional time range)
- Choice of whether seasonal periods affect the vigil Mass calculation
- Date-specific exceptions (holidays, special events)
- Periods with schedule suppression or replacement (e.g. summer hours)
- Automatic "next Mass" display
- Dedicated frontend widget module, configurable per church
- Test mode with simulated date
- Option to keep data in the database on uninstall (useful for clean updates)
- Full set of form save buttons (Save / Save & New / Save & Close / Close)
- Copyright and license footer in the backend, with automatically updated year
- Administrator interface available in Italian and English
- Built following Joomla coding standards (PSR-4 namespaces, MVC, ACL)
- Released under the GNU GPL v2 or later license

## Requirements

- Joomla! 5.x or 6.x
- PHP 8.1 or higher
- MySQL / MariaDB with InnoDB and utf8mb4 support

## Installation

1. From **System → Manage → Install**, upload the `pkg_messe_vX.X.X.zip` file (the package includes both the `com_messe` component and the `mod_messe` module).
2. On installation, the component automatically creates the required database tables.
3. When updating to a newer version, there is no need to uninstall the previous one first — simply install the new package over the existing installation.

## Component usage guide (com_messe)

### 1. Add a church

Go to **Components → Mass Schedule Manager → Churches → New**. Enter name, description, address and rite, then save.

### 2. Set up weekly schedules

On the church edit screen, for each schedule type (Weekday, Vigil, Holiday) you can add one or more time slots, specifying hour, minutes and an optional label (e.g. "Sung Mass"). In the "Days" field you can restrict a time slot to specific weekdays (numbers 0–6, where 0 = Sunday, comma-separated, e.g. `2,4` for Tuesday and Thursday); leave it empty to apply the time slot to every day of the selected schedule type. On the frontend, the weekday is automatically shown by name (e.g. "Tuesday, Thursday") next to the time.

### 3. Vigil (Anticipated) Mass Mode

Each church can handle the Mass that anticipates a midweek feast (e.g. the evening before November 1st) differently. In the **"Vigil-Anticipation Mass Mode"** card you can choose between:

- **None** — this church has no anticipated Mass;
- **Same time as the Saturday Vigil Mass** — automatically reuses the times already entered in the "Vigil" section;
- **Specific dedicated schedule** — uses the times entered in the "Dedicated Vigil-Anticipation Schedule" section (identical to the others, with "Days" field support);
- **Last evening weekday Mass** — automatic calculation: takes the last weekday Mass of the day, with an optional min/max time range (see Component Options) it must fall within, otherwise no vigil Mass is shown that day.

If a **seasonal period** (e.g. "Summer Schedule") is active that day, you can choose whether it should also determine the vigil Mass time ("Apply seasonal periods to the vigil Mass" option, enabled by default) or whether the vigil Mass should always be based on the normal schedule, ignoring periods.

### 4. Form buttons

- **Save** — saves and stays in edit mode on the same church
- **Save & New** — saves and opens a blank form to add a new church
- **Save & Close** — saves and returns to the church list
- **Close** — closes without saving

### 5. Date-specific exceptions

In the "Exceptions" section you can add a special time slot valid only for a specific date (day-month format), useful for patronal feasts or occasional events.

### 6. Periods (e.g. summer schedule)

In the "Periods" section you can define a date range or a list of months during which a schedule type (e.g. weekday) is suppressed or replaced with alternative time slots.

### 7. Component options

From **Components → Mass Schedule Manager → Options** you can configure:

- whether to highlight the next upcoming Mass;
- how many days ahead to consider for weekday (default 7), vigil (default 5) and special-event schedules;
- whether to apply a **time range** (minimum/maximum, e.g. 4 PM–8 PM) to the "Last evening weekday Mass" mode, or disable it entirely;
- whether to **show the weekday** in the "Vigil Masses" list (e.g. "5:30 PM Thursday" instead of just "5:30 PM");
- whether **seasonal periods** affect the vigil Mass calculation;
- test mode (with a simulated date) to preview how the schedule will look;
- whether to **keep data in the database** when the component is uninstalled (enabled by default — recommended if you plan to reinstall or update the package).

The same vigil-related options are also available at the individual **menu item** level, to override the global behavior on a specific page.

## Module usage guide (mod_messe)

1. Go to **Content → Site Modules → New → Mass Schedule**.
2. Select the church to display among the published ones.
3. Choose whether to show the next Mass, the full schedule, or both.
4. Set the module position in your template and the pages where it should appear.
5. The module parameters include the same vigil time-range, weekday-display and seasonal-period options available in the component.
6. Save and publish.

## Support and license

The component is released under the **GNU General Public License v2 or later**. For bug reports or support requests, please contact the author.

---
*Author: Gioacchino Cipriano*

*Extension developed with the support of Claude AI (Anthropic).*
