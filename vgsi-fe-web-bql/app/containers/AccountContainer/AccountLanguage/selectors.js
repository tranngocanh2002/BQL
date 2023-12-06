import { initialState } from "./reducer";

/**
 * Direct selector to the accountSecurity state domain
 */

const selectAccountLanguageDomain = (state) =>
  state.get("accountSecurity", initialState);

/**
 * Other specific selectors
 */

/**
 * Default selector used by AccountSecurity
 */

export { selectAccountLanguageDomain };
