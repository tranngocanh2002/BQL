import { fromJS } from "immutable";
import CombineCardListReducer from "../reducer";

describe("CombineCardListReducer", () => {
  it("returns the initial state", () => {
    expect(CombineCardListReducer(undefined, {})).toEqual(fromJS({}));
  });
});
