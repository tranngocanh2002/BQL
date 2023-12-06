import { fromJS } from 'immutable';
import dashboardReceptionReducer from '../reducer';

describe('dashboardReceptionReducer', () => {
  it('returns the initial state', () => {
    expect(dashboardReceptionReducer(undefined, {})).toEqual(fromJS({}));
  });
});
