import { fromJS } from "immutable";
import apartmentListReducer from "../reducer";

describe("formListReducer", () => {
  it("returns the initial state", () => {
    expect(apartmentListReducer(undefined, {})).toEqual(fromJS({}));
  });
});
