/*
 *
 * Dashboard actions
 *
 */

import {
  DEFAULT_ACTION,
  FETCH_COUNT_REPORT,
  FETCH_COUNT_REPORT_COMPLETE,
  FETCH_ANNOUNCEMENT,
  FETCH_ANNOUNCEMENT_COMPLETE,
  FETCH_REQUEST,
  FETCH_REQUEST_COMPLETE,
  FETCH_FINANCE,
  FETCH_FINANCE_COMPLETE,
  BOOKING_REVENUE,
  BOOKING_REVENUE_COMPLETE,
  BOOKING_LIST_REVENUE,
  BOOKING_LIST_REVENUE_COMPLETE,
  FETCH_APARTMENT,
  FETCH_APARTMENT_COMPLETE,
  FETCH_RESIDENT,
  FETCH_RESIDENT_COMPLETE,
  FETCH_MAINTENANCE,
  FETCH_MAINTENANCE_COMPLETE,
} from "./constants";

export function defaultAction() {
  return {
    type: DEFAULT_ACTION,
  };
}

export function fetchCountReport() {
  return {
    type: FETCH_COUNT_REPORT,
  };
}

export function fetchCountReportComplete(payload) {
  return {
    type: FETCH_COUNT_REPORT_COMPLETE,
    payload,
  };
}

export function fetchAnnouncement() {
  return {
    type: FETCH_ANNOUNCEMENT,
  };
}

export function fetchAnnouncementComplete(payload) {
  return {
    type: FETCH_ANNOUNCEMENT_COMPLETE,
    payload,
  };
}

export function fetchRequest(payload) {
  return {
    type: FETCH_REQUEST,
    payload,
  };
}

export function fetchRequestComplete(payload) {
  return {
    type: FETCH_REQUEST_COMPLETE,
    payload,
  };
}

export function fetchMaintenance(payload) {
  return {
    type: FETCH_MAINTENANCE,
    payload,
  };
}

export function fetchMaintenanceComplete(payload) {
  return {
    type: FETCH_MAINTENANCE_COMPLETE,
    payload,
  };
}

export function fetchFinance(payload) {
  return {
    type: FETCH_FINANCE,
    payload,
  };
}

export function fetchFinanceComplete(payload) {
  return {
    type: FETCH_FINANCE_COMPLETE,
    payload,
  };
}

export function fetchBookingRevenue(payload) {
  return {
    type: BOOKING_REVENUE,
    payload,
  };
}

export function fetchBookingRevenueComplete(payload) {
  return {
    type: BOOKING_REVENUE_COMPLETE,
    payload,
  };
}

export function fetchBookingListRevenue(payload) {
  return {
    type: BOOKING_LIST_REVENUE,
    payload,
  };
}

export function fetchBookingListRevenueComplete(payload) {
  return {
    type: BOOKING_LIST_REVENUE_COMPLETE,
    payload,
  };
}

export function fetchApartment() {
  return {
    type: FETCH_APARTMENT,
  };
}

export function fetchApartmentComplete(payload) {
  return {
    type: FETCH_APARTMENT_COMPLETE,
    payload,
  };
}

export function fetchResident() {
  return {
    type: FETCH_RESIDENT,
  };
}

export function fetchResidentComplete(payload) {
  return {
    type: FETCH_RESIDENT_COMPLETE,
    payload,
  };
}
