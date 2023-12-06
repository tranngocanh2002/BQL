/**
 *
 * Asynchronously loads the component for ResourceManager
 *
 */

import loadable from "loadable-components";

export default loadable(() => import("./index"));
