/**
 *
 * ServiceFeeCreate
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { FormattedMessage, injectIntl } from "react-intl";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectServiceFeeCreate from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import Page from "../../../components/Page/Page";
import { Menu, Icon, Result, Button } from "antd";
import { Switch, Route, withRouter, Redirect } from "react-router-dom";

import WaterLockFeeTemplatePage from "../ServiceDetailContainer/WaterServiceContainer/LockFeeTemplatePage";
import ElectricLockFeeTemplatePage from "../ServiceDetailContainer/ElectricServiceContainer/LockFeeTemplatePage";
import MotoPackingLockFeeTemplatePage from "../ServiceDetailContainer/MotoPackingServiceContainer/LockFeeTemplatePage";
import ManagementClusterLockFeeTemplatePage from "../ServiceDetailContainer/ManagementClusterServiceContainer/PaymentTemplatePage";
import { defaultAction, fetchAllService } from "./actions";
import messages from "./messages";

/* eslint-disable react/prefer-stateless-function */
export class ServiceFeeCreate extends React.PureComponent {
  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this.props.dispatch(fetchAllService());
  }

  render() {
    const { serviceFeeCreate } = this.props;

    if (serviceFeeCreate.loading || !serviceFeeCreate.items) {
      return null;
    }
    let electricService = serviceFeeCreate.items.find(
      (iii) => iii.service_base_url == "/electric"
    );
    let waterService = serviceFeeCreate.items.find(
      (iii) => iii.service_base_url == "/water"
    );
    let motoPackingService = serviceFeeCreate.items.find(
      (iii) => iii.service_base_url == "/moto-packing"
    );
    let managementClusterService = serviceFeeCreate.items.find(
      (iii) => iii.service_base_url == "/apartment-fee"
    );

    if (!waterService && !motoPackingService && !managementClusterService) {
      return (
        <Page inner className={"serviceListPage"}>
          <Result
            status="404"
            // title="Kh"
            subTitle={<FormattedMessage {...messages.emptyService} />}
            extra={
              <Button
                type="primary"
                onClick={() => this.props.history.push("/main/service/cloud")}
              >
                <FormattedMessage {...messages.addService} />
              </Button>
            }
          />
        </Page>
      );
    }

    return (
      <Page inner>
        <div>
          <Menu
            onClick={this.handleClick}
            selectedKeys={[this.props.location.pathname]}
            mode="horizontal"
            onSelect={({ key }) => {
              this.props.history.push(key);
            }}
            style={{ marginBottom: 16 }}
          >
            {!!managementClusterService && (
              <Menu.Item key="/main/service/create/apartment-fee">
                <FormattedMessage {...messages.managementFee} />
              </Menu.Item>
            )}
            {!!electricService && (
              <Menu.Item key="/main/service/create/electric">
                {<FormattedMessage {...messages.electricService} />}
              </Menu.Item>
            )}
            {!!waterService && (
              <Menu.Item key="/main/service/create/water">
                {<FormattedMessage {...messages.waterService} />}
              </Menu.Item>
            )}
            {!!motoPackingService && (
              <Menu.Item key="/main/service/create/moto-packing">
                {<FormattedMessage {...messages.parkingService} />}
              </Menu.Item>
            )}
          </Menu>
          <Switch>
            {!!electricService && (
              <Route
                path="/main/service/create/electric"
                render={(props) => (
                  <ElectricLockFeeTemplatePage
                    base_url={"/electric"}
                    electricServiceContainer={{
                      loading: false,
                      data: electricService,
                    }}
                  />
                )}
              />
            )}
            {!!waterService && (
              <Route
                path="/main/service/create/water"
                render={(props) => (
                  <WaterLockFeeTemplatePage
                    base_url={"/water"}
                    waterServiceContainer={{
                      loading: false,
                      data: waterService,
                    }}
                  />
                )}
              />
            )}
            {!!motoPackingService && (
              <Route
                path="/main/service/create/moto-packing"
                render={(props) => (
                  <MotoPackingLockFeeTemplatePage
                    base_url={"/moto-packing"}
                    motoPackingServiceContainer={{
                      loading: false,
                      data: motoPackingService,
                    }}
                  />
                )}
              />
            )}
            {!!managementClusterService && (
              <Route
                path="/main/service/create/apartment-fee"
                render={(props) => (
                  <ManagementClusterLockFeeTemplatePage
                    base_url={"/apartment-fee"}
                    managementClusterServiceContainer={{
                      loading: false,
                      data: managementClusterService,
                    }}
                  />
                )}
              />
            )}

            <Redirect
              to={`/main/service/create${
                (managementClusterService || waterService || motoPackingService)
                  .service_base_url
              }`}
            />
          </Switch>
        </div>
      </Page>
    );
  }
}

ServiceFeeCreate.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  serviceFeeCreate: makeSelectServiceFeeCreate(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "serviceFeeCreate", reducer });
const withSaga = injectSaga({ key: "serviceFeeCreate", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(ServiceFeeCreate));
