import { fromJS } from 'immutable';
import buildingInfomationReducer from '../reducer';

describe('buildingInfomationReducer', () => {
  it('returns the initial state', () => {
    expect(buildingInfomationReducer(undefined, {})).toEqual(fromJS({}));
  });
});
