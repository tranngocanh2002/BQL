/**
 *
 * Asynchronously loads the component for LucidList
 *
 */

import loadable from "loadable-components";

export default loadable(() => import("./index"));
