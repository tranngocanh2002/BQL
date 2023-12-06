import { fromJS } from "immutable";
import combineCardActiveReducer from "../reducer";

describe("combineCardActiveReducer", () => {
  it("returns the initial state", () => {
    expect(combineCardActiveReducer(undefined, {})).toEqual(fromJS({}));
  });
});
