import { fromJS } from "immutable";
import apartmentAddReducer from "../reducer";

describe("maintainDetailReducer", () => {
  it("returns the initial state", () => {
    expect(maintainDetailReducer(undefined, {})).toEqual(fromJS({}));
  });
});
