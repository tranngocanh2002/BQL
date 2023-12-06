import { fromJS } from 'immutable';
import oldDebitServiceContainerReducer from '../reducer';

describe('oldDebitServiceContainerReducer', () => {
  it('returns the initial state', () => {
    expect(oldDebitServiceContainerReducer(undefined, {})).toEqual(fromJS({}));
  });
});
