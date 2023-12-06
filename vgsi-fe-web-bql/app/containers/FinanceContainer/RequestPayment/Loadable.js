/**
 *
 * Asynchronously loads the component for RequestPayment
 *
 */

import loadable from "loadable-components";

export default loadable(() => import("./index"));
