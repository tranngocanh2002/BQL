import { fromJS } from "immutable";
import supplierListReducer from "../reducer";

describe("supplierListReducer", () => {
  it("returns the initial state", () => {
    expect(supplierListReducer(undefined, {})).toEqual(fromJS({}));
  });
});
