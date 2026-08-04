# Mass Schedule Manager (com_messe + mod_messe)

[![License: GPL v2](https://img.shields.io/badge/License-GPLv2-blue.svg)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)

*[Leggi questo in italiano](README.md)*

A Joomla component to manage and publish Mass schedules for one or more churches, with configurable vigil (anticipated) Masses, holiday exceptions, special periods (e.g. summer schedules), and a companion module to display them anywhere on the site.

## Description

**Mass Schedule Manager** is designed for parishes, dioceses and religious community websites that need a simple, always up-to-date way to publish Mass schedules for one or more churches.

From the administrator panel you can:

- manage an unlimited number of churches, each with name, description, address and rite (Roman or Ambrosian);
- set up weekly schedules split into **weekday**, **vigil** (Saturday evening) and **holiday**, with the option to restrict a single time slot to specific days of the week;
- flexibly configure, per church, **how the anticipated ("vigil") Mass for a midweek feast** is handled: none, same time as the Saturday vigil Mass, a specific dedicated schedule, or automatic calculation from the last evening weekday Mass (with an optional time range);
- choose whether **active seasonal periods** (e.g. summer schedule) should also determine the vigil Mass time;
- manage **date-specific exceptions** (e.g. a patronal feast);
- manage **periods** (date ranges or whole months) during which a schedule type can be suppressed or replaced with an alternative one;
- display the next upcoming Mass and the full schedule on the frontend, including the weekday when relevant;
- use a built-in test mode with a simulated date.

The package also includes **mod_messe**, a frontend module that displays the next Mass time (or the full schedule) for a specific church in any module position of your template.

## Key features

- Multi-church management with Roman/Ambrosian rite support
- Weekday, vigil and holiday schedules, with optional restriction to specific weekdays
- Configurable vigil/anticipated Mass per church, with 4 modes
- Choice of whether seasonal periods affect the vigil Mass calculation
- Date-specific exceptions and periods with schedule suppression/replacement
- Automatic "next Mass" display
- Dedicated frontend widget module
- Test mode with simulated date
- Option to keep data in the database on uninstall
- Full set of form save buttons (Save / Save & New / Save & Close / Close)
- Administrator interface available in Italian and English
- Built following Joomla coding standards (PSR-4 namespaces, MVC, ACL)

## Requirements

- Joomla! 5.x or 6.x
- PHP 8.1 or higher
- MySQL / MariaDB with InnoDB and utf8mb4 support

## Installation

Download the latest package from this repository's [Releases](../../releases) section (`pkg_messe_vX.X.X.zip`) and install it from **System → Manage → Install** in the Joomla backend. The package includes both the `com_messe` component and the `mod_messe` module.

To update, there's no need to uninstall the previous version first — simply install the new package over the existing installation.

## Quick usage guide

### Add a church
**Components → Mass Schedule Manager → Churches → New**. Enter name, description, address and rite.

### Weekly schedules
For each schedule type (Weekday, Vigil, Holiday) you can add one or more time slots with hour, minutes, an optional label, and a "Days" field (0-6, 0=Sunday, comma-separated) to restrict the time slot to specific days.

### Vigil (Anticipated) Mass Mode
For each church choose between: None / Same time as the Saturday Vigil / Specific dedicated schedule / Last evening weekday Mass (with an optional time range).

### Periods and exceptions
Manage seasonal schedules (e.g. summer) and special dates from the respective sections of the church form.

### mod_messe module
**Content → Site Modules → New → Mass Schedule** to display a church's schedule in any template position.

For the full options guide, see the [`docs/`](docs/) folder in this repository.

## Repository structure

```
.
├── com_messe/      Component source code
├── mod_messe/      Module source code
├── docs/           Extended documentation (IT/EN)
├── LICENSE         GPL v2 license text
└── README.md       This file (Italian) / README.en.md (English)
```

## Contributing / Reporting issues

Bug reports and feature requests are welcome in this repository's [Issues](../../issues) section.

## License

Released under the **GNU General Public License v2 or later**. See the [LICENSE](LICENSE) file for the full text.

## Author

**Gioacchino Cipriano** — [gioacchinocipriano.it](https://gioacchinocipriano.it/)

*Extension developed with the support of Claude AI (Anthropic).*
