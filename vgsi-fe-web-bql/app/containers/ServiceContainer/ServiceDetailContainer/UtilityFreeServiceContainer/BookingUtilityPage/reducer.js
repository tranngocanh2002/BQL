/*
 *
 * BookingUtilityPage reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION, FETCH_ALL_CONFIG, FETCH_ALL_CONFIG_COMPLETE,
  FETCH_APARTMENT, FETCH_APARTMENT_COMPLETE, CREATE_BOOKING, CREATE_BOOKING_COMPLETE, FETCH_BOOKING, FETCH_BOOKING_COMPLETE, FETCH_SLOT_FREE, FETCH_SLOT_FREE_COMPLETE
} from "./constants";

export const initialState = fromJS({
  loading: true,
  data: [],
  apartments: {
    loading: false,
    lst: []
  },
  create: {
    loading: false,
    success: false
  },
  bookings: {

  },
  freeSlot: {

  }
});

function bookingUtilityPageReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_ALL_CONFIG:
      return state.set('loading', true).set('data', [])
    case FETCH_ALL_CONFIG_COMPLETE:
      return state.set('loading', false).set('data', action.payload || [])
    case FETCH_APARTMENT:
      return state.setIn(['apartments', 'loading'], true)
    case FETCH_APARTMENT_COMPLETE:
      return state.setIn(['apartments', 'loading'], false)
        .setIn(['apartments', 'lst'], action.payload ? fromJS(action.payload) : -1)
    case CREATE_BOOKING:
      return state.setIn(['create', 'loading'], true).setIn(['create', 'success'], false)
    case CREATE_BOOKING_COMPLETE:
      return state.setIn(['create', 'loading'], false)
        .setIn(['create', 'success'], action.payload || false)
    case FETCH_SLOT_FREE: {
      const { service_utility_config_id, current_time } = action.payload
      let freeSlot = state.get('freeSlot');
      freeSlot = freeSlot.set(`slot-${service_utility_config_id}-${current_time}`, {
        loading: true,
        items: []
      })
      return state.set('freeSlot', freeSlot)
    }
    case FETCH_SLOT_FREE_COMPLETE: {
      const { service_utility_config_id, current_time, items } = action.payload
      let freeSlot = state.get('freeSlot');
      freeSlot = freeSlot.set(`slot-${service_utility_config_id}-${current_time}`, {
        loading: false,
        items
      })
      return state.set('freeSlot', freeSlot)
    }
    case FETCH_BOOKING: {
      const { service_utility_config_id, service_utility_free_id, dates } = action.payload
      let bookings = state.get('bookings');
      dates.forEach(element => {
        let currentBooking = bookings.get(`book-${service_utility_free_id}-${service_utility_config_id}-${element}`)
        if (!currentBooking) {
          currentBooking = {
            loading: true,
            items: [],
            statics: [],
          }
        } else {
          currentBooking = {
            ...currentBooking,
            loading: true,
          }
        }

        bookings = bookings.set(`book-${service_utility_free_id}-${service_utility_config_id}-${element}`, currentBooking)

      });


      return state.set('bookings', bookings)
    }
    case FETCH_BOOKING_COMPLETE:
      const { service_utility_config_id, service_utility_free_id, start_date, items } = action.payload
      let bookings = state.get('bookings');

      Object.keys(items).forEach(key => {
        let currentBooking = bookings.get(`book-${service_utility_free_id}-${service_utility_config_id}-${key}`)
        if (!currentBooking) {
          currentBooking = {
            loading: false,
            items: items[key].books,
            statics: items[key].statics,
          }
        } else {
          currentBooking = {
            ...currentBooking,
            loading: false,
            items: items[key].books,
            statics: items[key].statics,
          }
        }

        bookings = bookings.set(`book-${service_utility_free_id}-${service_utility_config_id}-${key}`, currentBooking)

      })


      return state.set('bookings', bookings)
    default:
      return state;
  }
}

export default bookingUtilityPageReducer;
