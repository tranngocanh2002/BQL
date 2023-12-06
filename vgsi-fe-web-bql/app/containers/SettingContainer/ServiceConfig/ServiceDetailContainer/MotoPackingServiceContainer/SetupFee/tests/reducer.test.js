import { fromJS } from 'immutable';
import SetupFeeMotoPackingPageReducer from '../reducer';

describe('SetupFeeMotoPackingPageReducer', () => {
  it('returns the initial state', () => {
    expect(SetupFeeMotoPackingPageReducer(undefined, {})).toEqual(fromJS({}));
  });
});
