/**
 *
 * Asynchronously loads the component for CashBook
 *
 */

import loadable from "loadable-components";

export default loadable(() => import("./index"));
