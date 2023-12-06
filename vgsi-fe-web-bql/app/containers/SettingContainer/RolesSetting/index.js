/**
 *
 * RolesCreate
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import { Exception } from "ant-design-pro";
import {
  Button,
  Checkbox,
  Col,
  Form,
  Icon,
  Input,
  Modal,
  Row,
  Select,
  Tooltip,
} from "antd";
import _ from "lodash";
import { withRouter } from "react-router";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Loader from "../../../components/Loader/Loader";
import Page from "../../../components/Page/Page";
import { config } from "../../../utils";
import messages from "../messages";
import {
  createAuthItemWeb,
  createGroupAuth,
  defaultAction,
  fetchAllPermission,
  fetchGroupAuthDetail,
} from "./actions";
import styles from "./index.less";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectRolesCreate from "./selectors";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

const { TextArea } = Input;

const FormItem = Form.Item;

const confirm = Modal.confirm;
/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class RolesSetting extends React.PureComponent {
  constructor(props) {
    super(props);

    const { group } = props.location.state || {};

    this.state = {
      group,
      groupPermissions: {
        BASIC: ["BASIC"],
      },
      id: group ? group.id : undefined,
      code: group ? group.code : undefined,
      name: group ? group.name : "",
      name_en: group ? group.name_en : "",
      description: group ? group.description : "",
      type: group ? group.type : "",
      permission_web: "",
    };
  }
  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this.props.dispatch(fetchAllPermission());
    const { id } = this.props.match.params;
    const { group } = this.props.location.state || {};
    if (!!id && !group) {
      this.props.dispatch(fetchGroupAuthDetail({ id }));
    }
  }

  handleOk = () => {
    const { groupPermissions, group, id } = this.state;
    const { dispatch, form } = this.props;
    const { validateFields } = form;
    validateFields((errors, values) => {
      if (errors) {
        return;
      }
      let data_role = _.flatMap(groupPermissions);
      if (data_role.length == 0) {
      }
      dispatch(createGroupAuth({ ...values, data_role, id }));
    });
  };
  handleSubmitPermission = () => {
    const { dispatch, form } = this.props;
    const { validateFields } = form;
    validateFields((errors, values) => {
      let data_permission = values.permission_web
        .split("  ")
        .map((item) => item.trim());
      dispatch(createAuthItemWeb({ codes: data_permission }));
    });
  };

  componentWillReceiveProps(nextProps) {
    const { data } = nextProps.rolesCreate.groupDetail;
    if (this.props.rolesCreate.groupDetail.data != data && !!data) {
      if (data.data_role) {
        let groupPermissions = {
          BASIC: ["BACSIC"],
        };
        this.props.rolesCreate.allPermission.lst.forEach((element) => {
          groupPermissions[element.name] = element.data
            .map((mm) => mm.name)
            .filter((mm) => data.data_role.some((xx) => xx == mm));
        });
        this.setState({
          id: data.id,
          code: data.code,
          name: data.name,
          name_en: data.name_en,
          description: data.description,
          type: data.type,
          groupPermissions,
          group: { ...data },
        });
      }
    }
    if (
      this.props.rolesCreate.allPermission.loading !=
        nextProps.rolesCreate.allPermission.loading &&
      !nextProps.rolesCreate.allPermission.loading
    ) {
      const { data_role } =
        this.state.group || this.props.rolesCreate.groupDetail.data || {};
      if (data_role) {
        let groupPermissions = {};
        nextProps.rolesCreate.allPermission.lst.forEach((element) => {
          groupPermissions[element.name] = element.data
            .map((mm) => mm.name)
            .filter((mm) => data_role.some((xx) => xx == mm));
        });

        this.setState({
          groupPermissions,
        });
      }
    }
  }

  render() {
    const { rolesCreate, form, language } = this.props;
    const { groupPermissions, code } = this.state;
    const { getFieldDecorator } = form;
    const formatMessage = this.props.intl.formatMessage;
    const {
      allPermission,
      isCreating,
      isCreateSuccess,
      groupDetail,
      isCreateAuthItem,
    } = rolesCreate;
    if (isCreateSuccess) {
      this.props.history.push("/main/setting/roles/list", {
        refresh: true,
      });
    }
    if (groupDetail.data === -1) {
      return (
        <Page inner>
          <Exception
            type="404"
            desc={formatMessage(messages.pageNotFound)}
            actions={
              <Button
                type="primary"
                onClick={() =>
                  this.props.history.push("/main/setting/roles/list")
                }
              >
                {formatMessage(messages.back)}
              </Button>
            }
          />
        </Page>
      );
    }
    {
      //Temp
      // let ALL_GROUP_ROLE_NAME = {}
      // allPermission.lst.forEach(ee => {
      //   ALL_GROUP_ROLE_NAME[ee.name] = ee.name
      // })
      // console.log(`ALL_GROUP_ROLE_NAME`, JSON.stringify(ALL_GROUP_ROLE_NAME))
      // let ALL_ROLE_NAME = {}
      // allPermission.lst.forEach(ee => {
      //   ee.data.forEach(iii => {
      //     ALL_ROLE_NAME[iii.name] = iii.name
      //   })
      // })
      // console.log(`ALL_GROUP_ROLE_NAME`, JSON.stringify(ALL_ROLE_NAME))
    }
    return (
      <div className={styles.rolesSettingPage} style={{ paddingBottom: 70 }}>
        <Page inner loading={groupDetail.loading}>
          <Form>
            <Row>
              <Col span={18}>
                <span className={styles.title}>
                  {!code
                    ? formatMessage(messages.addGroup)
                    : formatMessage(messages.editGroup)}
                </span>
              </Col>
            </Row>
            <Row gutter={24} style={{ marginTop: 12 }}>
              <Col lg={12} md={24}>
                <FormItem
                  label={
                    <span>
                      {formatMessage(messages.name)}
                      <Tooltip
                        title={formatMessage(messages.nameGroupPermission)}
                      >
                        <Icon
                          style={{ marginLeft: 4 }}
                          type="question-circle-o"
                        />
                      </Tooltip>
                    </span>
                  }
                  style={{ marginBottom: 0 }}
                  colon={false}
                >
                  {getFieldDecorator("name", {
                    initialValue: this.state.name,
                    rules: [
                      {
                        required: true,
                        message: formatMessage(
                          messages.emptyNameGroupPermission
                        ),
                        whitespace: true,
                      },
                    ],
                  })(
                    <Input
                      style={{ marginTop: 6 }}
                      maxLength={50}
                      placeholder=""
                    />
                  )}
                </FormItem>
                <FormItem
                  label={
                    <span>
                      {formatMessage(messages.name)} (EN)
                      <Tooltip
                        title={formatMessage(messages.nameEnGroupPermission)}
                      >
                        <Icon
                          style={{ marginLeft: 4 }}
                          type="question-circle-o"
                        />
                      </Tooltip>
                    </span>
                  }
                  style={{ marginBottom: 0 }}
                  colon={false}
                >
                  {getFieldDecorator("name_en", {
                    initialValue: this.state.name_en,
                    rules: [
                      {
                        required: true,
                        message: formatMessage(
                          messages.emptyNameEnGroupPermission
                        ),
                        whitespace: true,
                      },
                    ],
                  })(
                    <Input
                      style={{ marginTop: 6 }}
                      maxLength={50}
                      placeholder=""
                    />
                  )}
                </FormItem>
                <FormItem
                  label={
                    <span>
                      {formatMessage(messages.organization)}
                      <Tooltip
                        title={formatMessage(messages.tooltipOrganization)}
                      >
                        <Icon
                          style={{ marginLeft: 4 }}
                          type="question-circle-o"
                        />
                      </Tooltip>
                    </span>
                  }
                  style={{ marginBottom: 0 }}
                  colon={false}
                >
                  {getFieldDecorator("type", {
                    initialValue: this.state.type || 0,
                    rules: [
                      {
                        required: true,
                        message: formatMessage(messages.emptyStatusSupplier),
                        whitespace: true,
                        type: "number",
                      },
                    ],
                  })(
                    <Select
                      showSearch
                      placeholder={formatMessage(messages.selectOrganization)}
                      optionFilterProp="children"
                      // onChange={onChange}
                      filterOption={(input, option) =>
                        option.props.children
                          .toLowerCase()
                          .indexOf(input.toLowerCase()) >= 0
                      }
                    >
                      {config.TYPE_GROUP_USER.map((gr) => {
                        return (
                          <Select.Option key={`group-${gr.id}`} value={gr.id}>
                            {gr.name}
                          </Select.Option>
                        );
                      })}
                    </Select>
                  )}
                </FormItem>
              </Col>
              <Col lg={12} md={24}>
                <FormItem
                  label={
                    <span>
                      {formatMessage(messages.description)}
                      <Tooltip
                        title={formatMessage(messages.descriptionPermission)}
                      >
                        <Icon
                          style={{ marginLeft: 4 }}
                          type="question-circle-o"
                        />
                      </Tooltip>
                    </span>
                  }
                >
                  {getFieldDecorator("description", {
                    initialValue: this.state.description,
                    rules: [],
                  })(
                    <TextArea
                      rows={5}
                      style={{ marginTop: 6, marginBottom: 24 }}
                      maxLength={1000}
                    />
                  )}
                </FormItem>
              </Col>
            </Row>

            {/* <Row>
              <Col span={18}>
                <span className={styles.title}>Tất cả các quyền trên web</span>
              </Col>
            </Row>
            <Row gutter={24} style={{ marginTop: 12 }}>
              <Col lg={12} md={24}>
                <FormItem
                  label={
                    <span>
                      Permission Web
                      <Tooltip title="Permission Web">
                        <Icon
                          style={{ marginLeft: 4 }}
                          type="question-circle-o"
                        />
                      </Tooltip>
                    </span>
                  }
                >
                  {getFieldDecorator("permission_web", {
                    initialValue: this.state.permission_web,
                    rules: [],
                  })(
                    <TextArea
                      rows={5}
                      style={{ marginTop: 6, marginBottom: 12 }}
                    />
                  )}
                </FormItem>
              </Col>
            </Row>
            <Row gutter={24}>
              <Col span={18} style={{ marginBottom: 12 }}>
                <Button
                  danger
                  type="primary"
                  ghost
                  onClick={(e) => {
                    this.handleSubmitPermission();
                  }}
                  loading={isCreateAuthItem}
                >
                  Lưu các quyền
                </Button>
              </Col>
            </Row> */}

            {allPermission.loading && (
              <Row>
                <Loader inner hideText />
              </Row>
            )}
            {!allPermission.loading &&
              allPermission.lst.map((permissions, index) => {
                if (
                  permissions.name == "BASIC" ||
                  permissions.name == "ADMIN" ||
                  permissions.name == "ANNOUNCE" ||
                  permissions.name == "BOOKING" ||
                  permissions.name == "APARTMENT"
                )
                  return null;
                return (
                  <Row key={`pemissions-${index}`} style={{ marginBottom: 24 }}>
                    <Row
                      style={{ marginTop: 12, marginBottom: 8 }}
                      type="flex"
                      justify="space-between"
                      align="middle"
                    >
                      <span className={styles.title}>
                        {language === "en"
                          ? config.ALL_GROUP_ROLE_NAME_EN[permissions.name]
                          : config.ALL_GROUP_ROLE_NAME[permissions.name]}{" "}
                        <Tooltip
                          title={`Module ${
                            language === "en"
                              ? config.ALL_GROUP_ROLE_NAME_EN[permissions.name]
                              : config.ALL_GROUP_ROLE_NAME[permissions.name]
                          }`}
                        >
                          <Icon
                            style={{ marginLeft: 4 }}
                            type="question-circle-o"
                          />
                        </Tooltip>{" "}
                      </span>
                      <Checkbox
                        disabled={permissions.name == "BASIC"}
                        checked={
                          groupPermissions[permissions.name] &&
                          groupPermissions[permissions.name].length ==
                            permissions.data.length
                        }
                        onChange={(e) => {
                          if (e.target.checked) {
                            this.setState({
                              groupPermissions: {
                                ...groupPermissions,
                                [permissions.name]: permissions.data.map(
                                  (dd) => dd.name
                                ),
                              },
                            });
                          } else {
                            this.setState({
                              groupPermissions: {
                                ...groupPermissions,
                                [permissions.name]: [],
                              },
                            });
                          }
                        }}
                      >
                        {formatMessage(messages.all)}
                      </Checkbox>
                    </Row>
                    <Col className={styles.separator} />
                    <Col>
                      <Row>
                        {permissions.data
                          .filter((_) => _.name !== "RESIDENT_OLD_LIST")
                          .map((data, row) => {
                            return (
                              <Col
                                style={{ marginTop: 20 }}
                                key={row}
                                lg={5}
                                md={7}
                                offset={1}
                              >
                                <Checkbox
                                  checked={
                                    groupPermissions[permissions.name] &&
                                    groupPermissions[permissions.name].some(
                                      (rr) => rr == data.name
                                    )
                                  }
                                  disabled={permissions.name == "BASIC"}
                                  onChange={(e) => {
                                    if (e.target.checked) {
                                      if (groupPermissions[permissions.name]) {
                                        this.setState({
                                          groupPermissions: {
                                            ...groupPermissions,
                                            [permissions.name]:
                                              groupPermissions[
                                                permissions.name
                                              ].concat([data.name]),
                                          },
                                        });
                                      } else {
                                        this.setState({
                                          groupPermissions: {
                                            ...groupPermissions,
                                            [permissions.name]: [data.name],
                                          },
                                        });
                                      }
                                    } else {
                                      if (groupPermissions[permissions.name]) {
                                        this.setState({
                                          groupPermissions: {
                                            ...groupPermissions,
                                            [permissions.name]:
                                              groupPermissions[
                                                permissions.name
                                              ].filter((dd) => dd != data.name),
                                          },
                                        });
                                      } else {
                                        this.setState({
                                          groupPermissions: {
                                            ...groupPermissions,
                                            [permissions.name]: [],
                                          },
                                        });
                                      }
                                    }
                                  }}
                                >
                                  {language === "en"
                                    ? data.description_en
                                    : data.description}
                                </Checkbox>
                              </Col>
                            );
                          })}
                      </Row>
                    </Col>
                  </Row>
                );
              })}
          </Form>
        </Page>
        <div
          style={{
            position: "absolute",
            left: 0,
            right: 0,
            bottom: 0,
            height: 72,
            background: "white",
            boxShadow: " 0 0 20px rgba(0, 0, 0, 0.1)",
          }}
        >
          <Row
            type="flex"
            align="middle"
            justify="end"
            style={{ height: "100%", paddingRight: 30 }}
          >
            <Col span={4} style={{ textAlign: "right" }}>
              <Row>
                <Col span={11}>
                  <Button
                    block
                    ghost
                    type="danger"
                    onClick={(e) => {
                      confirm({
                        autoFocusButton: null,
                        title: code
                          ? formatMessage(messages.confirmCancelEditGroup)
                          : formatMessage(messages.confirmCancelCreateGroup),
                        onOk: () => {
                          this.props.history.goBack();
                        },
                        onCancel() {
                          console.log("Cancel");
                        },
                      });
                    }}
                    disabled={isCreating}
                  >
                    {formatMessage(messages.cancel)}
                  </Button>
                </Col>
                <Col offset={1} span={12}>
                  <Button
                    block
                    type="primary"
                    ghost
                    onClick={(e) => {
                      // this.props.history.push('/roles/create')
                      this.handleOk();
                    }}
                    loading={isCreating}
                  >
                    {formatMessage(messages.save)}
                  </Button>
                </Col>
              </Row>
            </Col>
          </Row>
        </div>
      </div>
    );
  }
}

RolesSetting.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  rolesCreate: makeSelectRolesCreate(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "rolesCreate", reducer });
const withSaga = injectSaga({ key: "rolesCreate", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(injectIntl(RolesSetting)));
