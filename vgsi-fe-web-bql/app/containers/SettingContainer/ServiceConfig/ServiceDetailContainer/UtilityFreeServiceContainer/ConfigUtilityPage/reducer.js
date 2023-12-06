/*
 *
 * ConfigUtilityPage reducer
 *
 */

import { fromJS } from "immutable";
import {
  DEFAULT_ACTION,
  FETCH_ALL_CONFIG,
  FETCH_ALL_CONFIG_COMPLETE,
  CREATE_CONFIG,
  CREATE_CONFIG_COMPLETE,
  FETCH_CONFIG_PRICE,
  FETCH_CONFIG_PRICE_COMPLETE,
  CREATE_CONFIG_PRICE,
  CREATE_CONFIG_PRICE_COMPLETE,
  DELETE_CONFIG_PRICE,
  DELETE_CONFIG_PRICE_COMPLETE,
  DELETE_CONFIG_PLACE,
  DELETE_CONFIG_PLACE_COMPLETE,
  UPDATE_CONFIG,
  UPDATE_CONFIG_COMPLETE,
} from "./constants";

export const initialState = fromJS({
  loading: false,
  data: [],
  creating: false,
  createSuccess: false,
  updating: false,
  updateSuccess: false,
  creatingPrice: false,
  createPriceSuccess: false,
  deletePlace: false,
  deletePlaceSuccess: false,
});

function configUtilityPageReducer(state = initialState, action) {
  switch (action.type) {
    case DEFAULT_ACTION:
      return initialState;
    case FETCH_ALL_CONFIG:
      return state.set("loading", true).set("data", []);
    case FETCH_ALL_CONFIG_COMPLETE:
      return state.set("loading", false).set("data", action.payload || []);
    case CREATE_CONFIG:
      return state.set("creating", true).set("createSuccess", false);
    case CREATE_CONFIG_COMPLETE:
      return state.set("creating", false).set("createSuccess", action.payload);
    case UPDATE_CONFIG:
      return state.set("updating", true).set("updateSuccess", false);
    case UPDATE_CONFIG_COMPLETE:
      return state.set("updating", false).set("updateSuccess", action.payload);
    case CREATE_CONFIG_PRICE:
      return state.set("creatingPrice", true).set("createPriceSuccess", false);
    case CREATE_CONFIG_PRICE_COMPLETE:
      return state
        .set("creatingPrice", false)
        .set("createPriceSuccess", action.payload);
    case FETCH_CONFIG_PRICE: {
      let configs = state.get(`config-${action.payload}`);
      if (!configs) {
        configs = {
          loading: true,
          data: [],
        };
      }
      configs = {
        ...configs,
        loading: true,
      };
      return state.set(`config-${action.payload}`, configs);
    }
    case DELETE_CONFIG_PRICE: {
      let configs = state.get(
        `config-${action.payload.service_utility_config_id}`
      );
      if (!configs) {
        configs = {
          loading: true,
          data: [],
        };
      }
      configs = {
        ...configs,
        loading: true,
      };
      return state.set(
        `config-${action.payload.service_utility_config_id}`,
        configs
      );
    }
    case DELETE_CONFIG_PRICE_COMPLETE: {
      let configs = state.get(
        `config-${action.payload.service_utility_config_id}`
      );
      if (!configs) {
        configs = {
          loading: true,
          data: [],
        };
      }
      configs = {
        ...configs,
        loading: false,
      };
      return state.set(
        `config-${action.payload.service_utility_config_id}`,
        configs
      );
    }
    case DELETE_CONFIG_PLACE:
      return state.set("deletePlace", true).set("deletePlaceSuccess", false);
    case DELETE_CONFIG_PLACE_COMPLETE:
      return state
        .set("deletePlace", false)
        .set("deletePlaceSuccess", action.payload);
    case FETCH_CONFIG_PRICE_COMPLETE: {
      let configs = state.get(
        `config-${action.payload.service_utility_config_id}`
      );
      if (!configs) {
        configs = {
          loading: true,
          data: [],
        };
      }
      configs = {
        ...configs,
        loading: false,
        data: action.payload.items,
      };
      return state.set(
        `config-${action.payload.service_utility_config_id}`,
        configs
      );
    }
    default:
      return state;
  }
}

export default configUtilityPageReducer;
