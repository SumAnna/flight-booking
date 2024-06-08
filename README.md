# Flight Booking System

## Overview

This flight booking system, built with Laravel and Vue.js, efficiently manages flights, bookings, and user profiles. It provides robust features such as flight listings, bookings, and cancellations, all underpinned by thorough validations and sophisticated error handling mechanisms. The system is designed to handle data asynchronously, using background tasks for efficient data management and a responsive user experience.

## Table of Contents
- [Installation](#installation)
  - [Prerequisites](#prerequisites)
  - [Steps](#steps)
- [Configuration](#configuration)
- [Running the Project](#running-the-project)
- [Features](#features)
  - [Flight Management](#flight-management)
  - [Booking Management](#booking-management)
  - [User Management](#user-management)
- [Testing](#testing)
  - [Unit and Feature Tests](#unit-and-feature-tests)
  - [Vue Component Tests](#vue-component-tests)

## Installation

### Prerequisites

Ensure you have the following installed:
- PHP 8.x
- Composer
- Node.js and npm
- MySQL

### Steps

1. Clone the repository:
   ```bash
   git clone https://github.com/SumAnna/flight-booking.git
   cd flight-booking
   ```
2. Install PHP dependencies:
   ```bash
   composer install
   ```
3. Install Node.js dependencies:
   ```bash
   npm install
   ```
4. Copy the .env.example to .env:
   ```bash
   cp .env.example .env
   ```
5. Copy the .env.example to .env.testing:
   ```bash
   cp .env .env.testing
   ```
6. Generate an application key:
   ```bash
   php artisan key:generate
   ```
7. Build the front-end assets:
   ```bash
   npm run dev
   ```
8. Run the database migrations:
   ```bash
   php artisan migrate
   ```
9. Import flight data from a JSON file into the database using:
   ```bash
   php artisan import:flight-data
   ```
10. Start the Laravel queue worker to process the jobs scheduled by the previous command:
    ```bash
    php artisan queue:work
    ```


## Configuration
Ensure you configure your .env file with the correct database credentials and other necessary configurations.

## Running the Project
1. Build the frontend assets:
   ```bash
   npm run dev
   ```
2. Start the development server:
   ```bash
   php artisan serve
   ```

## Features
### Flight Management
<ins>List Flights: </ins>Display available flights.<br />
<ins>Flight Details: </ins>View details of a specific flight, including segments and the number of available seats.<br />
<ins>Import Flight Data: </ins>Uses the <b>php artisan import:flight-data</b> command to asynchronously import flight data from a JSON file to the database, which schedules background jobs that are processed by the Laravel queue worker.<br />
### Booking Management
<ins>Book a Flight: </ins>Users can book a flight (without payment as of yet) if there are available seats.<br />
<ins>Cancel a Booking: </ins>Users can cancel their bookings.<br />
<ins>View Bookings: </ins>Users can view their bookings categorized as past, current, future, and cancelled.<br />
### User Management
<ins>Profile Management: </ins>Users can view and update their profile information.<br />

## Testing
### Unit and Feature Tests
To run the tests, execute the following command:
   ```bash
   php artisan test
   ```
### Vue Component Tests
To run Vue component tests, execute the following command:
   ```bash
   npm run test
   ```
