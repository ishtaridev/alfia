# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Stack

Laravel 13.x (PHP 8.4), Inertia.js v3, Vue 3, Tailwind CSS v4, SQLite, Vite, Laravel Fortify, Laravel Wayfinder.

## Users

- **Admins / Alfia staff:** create and manage travel offers, variants, pricing, reservations, users, and receive Telegram notifications when a new reservation is submitted.
- **Travellers / customers:** browse public offers and submit reservations without creating an account; they specify wilaya, room type, feeding preference, and traveller count.

## Product Purpose

Alfia is a reservation system for a local Algerian Umrah/Hajj travel operator. It lets the operator publish dated travel offers, collect traveller details, track reservation status, and alert admins instantly via Telegram so no booking is missed.

## Positioning

Built for Algerian Umrah/Hajj travel: wilaya-based customer capture, Arabic-friendly UI with RTL readiness, and real-time Telegram admin alerts set it apart from generic booking tools.

## Operating Context

- Admins work inside an authenticated back-office dashboard with stats, charts, recent reservations, and CRUD for users, offers, and reservations.
- Travellers use a public, no-account reservation flow linked to a specific offer variant (travel date + airport).
- Notifications are sent to a Telegram group configured via `TELEGRAM_BOT_TOKEN` and `TELEGRAM_ADMIN_GROUP_ID`.
- The app supports three locales: English, French, and Arabic.
- SQLite is the default database in local development.

## Capabilities and Constraints

- Offer/variant/pricing/reservation data model is fixed.
- Reservations have statuses (confirmed, pending, cancelled), room types, optional feeding, and a total price.
- Route model binding uses offer `code` rather than numeric IDs.
- Multi-language UI; Arabic may require RTL considerations.
- No public user accounts for travellers; only admin users authenticate.

## Brand Commitments

- Name: Alfia.
- Typeface: ITCHandleGothicArabic-Bold (`resources/fonts/itc.ttf`).
- Primary color: `#184a6d` (currently applied as the main background/sidebar base in `resources/css/app.css`).
- Desired brand feel: faithful, trusted, spiritually resonant for Umrah/Hajj travel.

## Evidence on Hand

- No real customer testimonials, case studies, or photography assets are present in the repository; future marketing surfaces must not fabricate them.
- Setup instructions for Telegram bot integration exist in `TELEGRAM_SETUP.md`.

## Product Principles

1. Trust first: every screen should reinforce reliability for a spiritually significant purchase.
2. Local clarity: wilaya, airport, date, and language choices are treated as first-class context, not afterthoughts.
3. Admin peace of mind: reservations surface immediately and clearly, with Telegram as the safety net.
4. No-traveller-account simplicity: the public flow is fast and account-free.

## Accessibility & Inclusion

- Multi-language support (EN/FR/AR) is required; Arabic RTL should be evaluated for future surfaces.
- No specific WCAG target has been established yet.
