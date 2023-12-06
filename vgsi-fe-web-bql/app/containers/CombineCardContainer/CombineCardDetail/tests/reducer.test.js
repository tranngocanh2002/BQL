import { fromJS } from "immutable";
import CombineCardDetailReducer from "../reducer";

describe("CombineCardDetailReducer", () => {
  it("returns the initial state", () => {
    expect(CombineCardDetailReducer(undefined, {})).toEqual(fromJS({}));
  });
});
