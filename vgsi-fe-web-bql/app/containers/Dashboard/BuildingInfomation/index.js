/**
 *
 * BuildingInfomation
 *
 */

import { Button, Col, Empty, Row, Typography } from "antd";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { debounce } from "lodash";
import PropTypes from "prop-types";
import React from "react";
import { injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import WithRole from "../../../components/WithRole";
import { getFullLinkImage } from "../../../connection";
import {
  selectAuthGroup,
  selectBuildingCluster,
} from "../../../redux/selectors";
import { config } from "../../../utils";
import { CardElectricInfomation } from "./CardElectricInfomation";
import { CardManagementInfomation } from "./CardManagementInfomation";
import { CardMotoPackingInfomation } from "./CardMotoPackingInfomation";
import { CardWaterInfomation } from "./CardWaterInfomation";
import { fetchAllService } from "./actions";
import "./index.less";
import messages from "./messages";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectBuildingInfomationPage from "./selectors";
const { Paragraph } = Typography;

const RowResponsiveProps = {
  style: {
    marginBottom: 16,
    display: "flex",
    flexWrap: "nowrap",
    overflowX: "auto",
  },
};

const topColResponsiveProps = {
  style: {
    flex: "0 0 auto",
    paddingRight: 16,
    paddingBottom: 16,
  },
  md: 12,
  lg: 10,
  xl: 8,
  xxl: 6,
};

const topColResponsiveProps1 = {
  style: {
    flex: "0 0 auto",
    paddingBottom: 16,
  },
  md: 12,
  lg: 10,
  xl: 8,
  xxl: 6,
};

/* eslint-disable react/prefer-stateless-function */
export class BuildingInfomation extends React.PureComponent {
  state = {
    hotline: [],
    windowWidth: window.innerWidth,
  };
  handleResize = (e) => {
    this.setState({ windowWidth: window.innerWidth });
  };

  componentDidMount() {
    window.addEventListener("resize", debounce(this.handleResize, 300));
    this.props.dispatch(fetchAllService());
  }
  componentWillUnMount() {
    window.removeEventListener("resize", debounce(this.handleResize, 300));
  }

  renderInfo = (data) => {
    let hotline = [];
    try {
      hotline = JSON.parse(data.hotline);
    } catch (error) {
      hotline = [
        {
          title: this.props.intl.formatMessage(messages.cskh),
          phone: data.hotline,
        },
      ];
    }
    return (
      <Row
        style={{
          backgroundColor: "#fff",
          marginBottom: "32px",
          display: "flex",
        }}
      >
        <Col
          xl={8}
          lg={7}
          md={24}
          style={{
            padding: "0px",
            display: !!data.medias && !!data.medias.imageUrl ? null : "flex",
          }}
        >
          {!!data.medias && !!data.medias.imageUrl ? (
            <img
              src={getFullLinkImage(data.medias.imageUrl)}
              width="100%"
              height={!!data.medias && !!data.medias.imageUrl ? null : "100%"}
            />
          ) : (
            <Empty
              style={{
                alignSelf: "center",
                width: "100%",
                fontSize: this.state.windowWidth < 1366 ? 14 : 18,
              }}
              description={this.props.intl.formatMessage(messages.noImage)}
              image="https://gw.alipayobjects.com/zos/antfincdn/ZHrcdLPrvN/empty.svg"
            >
              <WithRole
                roles={[config.ALL_ROLE_NAME.SETTING_BUILDING_INFOMATION]}
              >
                <Button
                  onClick={(e) => {
                    this.props.history.push(
                      "/main/setting/building/infomation"
                    );
                  }}
                >
                  <span
                    style={{
                      fontSize: this.state.windowWidth < 1366 ? 14 : 18,
                    }}
                  >
                    {this.props.intl.formatMessage(messages.setting)}
                  </span>
                </Button>
              </WithRole>
            </Empty>
          )}
        </Col>
        <Col
          xl={16}
          lg={17}
          md={24}
          style={{
            alignContent: "center",
            alignItems: "center",
            justifyItems: "center",
            justifyContent: "center",
            paddingLeft: "32px",
            paddingRight: "32px",
          }}
        >
          <Row className="rowItem">
            <strong
              style={{ fontSize: this.state.windowWidth < 1366 ? 18 : 20 }}
            >
              {""}
              {/* {this.props.intl.formatMessage(messages.generalInformation)} */}
            </strong>
          </Row>
          <Row className="rowItem">
            <Col span={4}>
              <span
                style={{ fontSize: this.state.windowWidth < 1366 ? 14 : 16 }}
              >
                {this.props.intl.formatMessage(messages.managementName)}
              </span>
            </Col>
            <Col
              offset={1}
              span={19}
              style={{
                fontWeight: "bold",
                fontSize: this.state.windowWidth < 1366 ? 14 : 16,
              }}
            >
              {data.name}
            </Col>
          </Row>
          <Row className="rowItem">
            <Col span={4}>
              <span
                style={{ fontSize: this.state.windowWidth < 1366 ? 14 : 16 }}
              >
                {this.props.intl.formatMessage(messages.domain)}
              </span>
            </Col>
            <Col
              offset={1}
              span={19}
              style={{
                fontWeight: "bold",
                fontSize: this.state.windowWidth < 1366 ? 14 : 16,
              }}
            >
              {data.domain}
            </Col>
          </Row>
          <Row className="rowItem">
            <Col span={4}>
              <span
                style={{ fontSize: this.state.windowWidth < 1366 ? 14 : 16 }}
              >
                {this.props.intl.formatMessage(messages.city)}
              </span>
            </Col>
            <Col
              offset={1}
              span={19}
              style={{
                fontWeight: "bold",
                fontSize: this.state.windowWidth < 1366 ? 14 : 16,
              }}
            >
              {data.city_name}
            </Col>
          </Row>
          <Row className="rowItem">
            <Col span={4}>
              <span
                style={{ fontSize: this.state.windowWidth < 1366 ? 14 : 16 }}
              >
                {this.props.intl.formatMessage(messages.address)}
              </span>
            </Col>
            <Col
              offset={1}
              span={19}
              style={{
                fontWeight: "bold",
                fontSize: this.state.windowWidth < 1366 ? 14 : 16,
              }}
            >
              {data.address}
            </Col>
          </Row>
          <Row className="rowItem">
            <Col span={4}>
              <span
                style={{ fontSize: this.state.windowWidth < 1366 ? 14 : 16 }}
              >
                {this.props.intl.formatMessage(messages.introduce)}
              </span>
            </Col>
            <Col
              offset={1}
              span={19}
              style={{
                whiteSpace: "pre-wrap",
                fontSize: this.state.windowWidth < 1366 ? 14 : 16,
              }}
            >
              {data.description}
            </Col>
          </Row>
          <Row className="rowItem">
            <Col span={4}>
              <span
                style={{ fontSize: this.state.windowWidth < 1366 ? 14 : 16 }}
              >
                {this.props.intl.formatMessage(messages.email)}
              </span>
            </Col>
            <Col
              offset={1}
              span={19}
              style={{
                fontWeight: "bold",
                fontSize: this.state.windowWidth < 1366 ? 14 : 16,
              }}
            >
              {data.email}
            </Col>
          </Row>
          <Row className="rowItem">
            <Col span={4}>
              <span
                style={{ fontSize: this.state.windowWidth < 1366 ? 14 : 16 }}
              >
                {this.props.intl.formatMessage(messages.hotline)}
              </span>
            </Col>
            <Col
              offset={1}
              span={19}
              style={{
                fontWeight: "bold",
                fontSize: this.state.windowWidth < 1366 ? 14 : 16,
              }}
            >
              {hotline &&
                hotline.map((hl, index) => {
                  return (
                    <Row
                      key={`hotline-index-${index}`}
                      style={{ marginBottom: 4 }}
                    >
                      <Col span={14}>
                        {this.props.language === "vi" ? hl.title : hl.title_en}:{" "}
                        {hl.phone}
                      </Col>
                    </Row>
                  );
                })}
            </Col>
          </Row>
        </Col>
      </Row>
    );
  };

  render() {
    const { data } = this.props.buildingCluster;
    const { auth_group, language } = this.props;
    const { items, loading } = this.props.buildingInfomation;
    let { formatMessage } = this.props.intl;

    if (!data && loading && items.length == 0) {
      return <div />;
    } else {
      let electric =
        items.length && items.find((x) => x.service_base_url === "/electric");
      let water =
        items.length && items.find((x) => x.service_base_url === "/water");
      let motoPacking =
        items.length &&
        items.find((x) => x.service_base_url === "/moto-packing");
      let management =
        items.length &&
        items.find((x) => x.service_base_url === "/apartment-fee");
      return (
        <Row>
          {this.renderInfo(data)}
          <Row {...RowResponsiveProps}>
            {electric ? (
              <CardElectricInfomation
                language={language}
                formatMessage={formatMessage}
                electric={electric}
                auth_group={auth_group}
                {...topColResponsiveProps}
              />
            ) : (
              <div />
            )}
            {water ? (
              <CardWaterInfomation
                language={language}
                formatMessage={formatMessage}
                water={water}
                auth_group={auth_group}
                {...topColResponsiveProps}
              />
            ) : (
              <div />
            )}
            {motoPacking ? (
              <CardMotoPackingInfomation
                language={language}
                formatMessage={formatMessage}
                motoPacking={motoPacking}
                auth_group={auth_group}
                {...topColResponsiveProps}
              />
            ) : (
              <div />
            )}
            {management ? (
              <CardManagementInfomation
                language={language}
                formatMessage={formatMessage}
                management={management}
                {...topColResponsiveProps1}
              />
            ) : (
              <div />
            )}
          </Row>
        </Row>
      );
    }
  }
}

BuildingInfomation.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  buildingCluster: selectBuildingCluster(),
  buildingInfomation: makeSelectBuildingInfomationPage(),
  auth_group: selectAuthGroup(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "buildingInfomation", reducer });
const withSaga = injectSaga({ key: "buildingInfomation", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(BuildingInfomation));
