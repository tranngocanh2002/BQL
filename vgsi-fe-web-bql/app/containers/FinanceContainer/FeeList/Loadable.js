/**
 *
 * Asynchronously loads the component for FeeList
 *
 */

import loadable from "loadable-components";

export default loadable(() => import("./index"));
