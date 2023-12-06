/**
 *
 * Asynchronously loads the component for MainLayout
 *
 */

import loadable from 'loadable-components';

export default loadable(() => import('./index'));
