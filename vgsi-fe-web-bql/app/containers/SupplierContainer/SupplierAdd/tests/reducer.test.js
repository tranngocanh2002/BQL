import { fromJS } from "immutable";
import supplierAddReducer from "../reducer";

describe("contractorAddReducer", () => {
  it("returns the initial state", () => {
    expect(supplierAddReducer(undefined, {})).toEqual(fromJS({}));
  });
});
