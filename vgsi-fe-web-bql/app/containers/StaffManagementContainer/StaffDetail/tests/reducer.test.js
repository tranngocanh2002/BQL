import { fromJS } from "immutable";
import staffDetailReducer from "../reducer";

describe("staffDetailReducer", () => {
  it("returns the initial state", () => {
    expect(staffDetailReducer(undefined, {})).toEqual(fromJS({}));
  });
});
