# Flight Booking System

## Overview

This is a flight booking system built with Laravel and Vue.js, designed to manage flights, bookings, and user profiles. It includes features such as flight listing, booking, and cancellation, with appropriate validations and error handling.

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
   ```bash
2. Install PHP dependencies:
   ```bash
   composer install
   ```bash
3. Install Node.js dependencies:
   ```bash
   npm install
   ```bash
4. Copy the .env.example to .env:
   ```bash
   cp .env.example .env
   ```bash
5. Copy the .env.example to .env.testing:
   ```bash
   cp .env .env.testing
   ```bash
6. Generate an application key:
   ```bash
   php artisan key:generate
   ```bash
7. Build the front-end assets:
   ```bash
   npm run dev
   ```bash

## Configuration
Ensure you configure your .env file with the correct database credentials and other necessary configurations.

## Running the Project
1. Build the frontend assets:
   ```bash
   npm run dev
   ```bash
2. Start the development server:
   ```bash
   php artisan serve
   ```bash

## Features
### Flight Management
List Flights: Display available flights.<br />
Flight Details: View details of a specific flight, including segments and the number of available seats.<br />
### Booking Management
Book a Flight: Users can book a flight (without payment as of yet) if there are available seats.<br />
Cancel a Booking: Users can cancel their bookings.<br />
View Bookings: Users can view their bookings categorized as past, current, future, and cancelled.<br />
### User Management
Profile Management: Users can view and update their profile information.<br />

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
