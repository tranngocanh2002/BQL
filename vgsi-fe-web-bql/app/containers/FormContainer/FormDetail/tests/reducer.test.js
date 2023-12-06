import { fromJS } from "immutable";
import formDetailReducer from "../reducer";

describe("formDetailReducer", () => {
  it("returns the initial state", () => {
    expect(formDetailReducer(undefined, {})).toEqual(fromJS({}));
  });
});
