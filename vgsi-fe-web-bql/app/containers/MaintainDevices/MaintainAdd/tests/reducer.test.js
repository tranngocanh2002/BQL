import { fromJS } from "immutable";
import apartmentAddReducer from "../reducer";

describe("maintainAddReducer", () => {
  it("returns the initial state", () => {
    expect(maintainAddReducer(undefined, {})).toEqual(fromJS({}));
  });
});
