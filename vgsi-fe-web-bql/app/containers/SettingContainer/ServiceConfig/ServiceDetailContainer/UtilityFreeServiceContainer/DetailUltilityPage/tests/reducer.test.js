import { fromJS } from 'immutable';
import DetailUltilityPageReducer from '../reducer';

describe('DetailUltilityPageReducer', () => {
  it('returns the initial state', () => {
    expect(DetailUltilityPageReducer(undefined, {})).toEqual(fromJS({}));
  });
});
