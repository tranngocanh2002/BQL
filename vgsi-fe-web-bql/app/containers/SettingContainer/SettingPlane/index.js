/**
 *
 * SettingPlane
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { injectIntl } from "react-intl";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectSettingPlane from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import messages from "./messages";
import { Col, Row, Tree, Icon, Form } from "antd";
import Page from "../../../components/Page/Page";
import { Bind, Debounce } from "lodash-decorators";
import Search from "antd/lib/input/Search";
import styles from "./index.less";
const { TreeNode } = Tree;

import Highlighter from "react-highlight-words";
import {
  selectBuildingCluster,
  selectAuthGroup,
} from "../../../redux/selectors";

import ClusterCreate from "./ClusterCreate";
import ClusterInfomation from "./ClusterInfomation";

import deepDiffer from "../../../utils/deepDiffer";
import config from "../../../utils/config";
import { defaultAction, getBuildingAreaAction } from "./actions";

import BuildingClusterInfomation from "../BuildingClusterInfomation";
import { parseTree } from "../../../utils";
import _ from "lodash";
const getParentKey = (key, tree) => {
  let parentKey;
  for (let i = 0; i < tree.length; i++) {
    const node = tree[i];
    if (node.children) {
      if (node.children.some((item) => item.key === key)) {
        parentKey = node.key;
      } else if (getParentKey(key, node.children)) {
        parentKey = getParentKey(key, node.children);
      }
    }
  }
  return parentKey;
};

const generateList = (data, dataList, level = 0) => {
  for (let i = 0; i < data.length; i++) {
    const { key, children, ...rest } = data[i];
    dataList.push({ key, title: key, ...rest, level });
    if (children) {
      generateList(children, dataList, level + 1);
    }
  }
};

const deleteNode = (key, parentKey, tree) => {
  for (let i = 0; i < tree.length; i++) {
    if (tree[i].key == parentKey) {
      tree[i].children = tree[i].children.filter((node) => node.key != key);
    } else {
      tree[i].children = deleteNode(key, parentKey, tree[i].children);
    }
  }
  return tree;
};

/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class SettingPlane extends React.PureComponent {
  constructor(props) {
    super(props);

    const { data } = props.buildingCluster;
    let gData = data
      ? [
          {
            name: data.name,
            key: "-1",
            children: [],
          },
        ]
      : [];
    let gDataList = [];
    generateList(gData, gDataList);
    this.state = {
      widthTree: 200,
      expandedKeys: ["-1"],
      searchValue: "",
      autoExpandParent: true,
      gData,
      gDataList,
      selectedKeys: ["-1"],
      adding: undefined,
      canCreateOrUpdate: props.auth_group.checkRole([
        config.ALL_ROLE_NAME.SETTING_BUILDING_AREA_CREATE_UPDATE,
      ]),
    };
  }

  expandTreeFrom = (key, gDataList, gData) => {
    return [
      key,
      ..._.flatMap(
        gDataList
          .map((item) => {
            if (item.key == key) {
              let res = [];
              let kkkk = getParentKey(item.key, gData);
              while (kkkk) {
                res.push(kkkk);
                kkkk = getParentKey(kkkk, gData);
              }
              return res;
            }
            return null;
          })
          .filter((rr) => !!rr)
      ),
    ];
  };

  onExpand = (expandedKeys) => {
    this.setState({
      expandedKeys,
      autoExpandParent: false,
    });
  };

  onChange = (e) => {
    const value = e.target.value;
    const expandedKeys = this.state.gDataList
      .map((item) => {
        if (item.name.toLowerCase().indexOf(value.toLowerCase()) > -1) {
          return getParentKey(item.key, this.state.gData);
        }
        return null;
      })
      .filter((item, i, self) => item && self.indexOf(item) === i);
    this.setState({
      expandedKeys,
      searchValue: value,
      autoExpandParent: true,
    });
  };

  @Bind()
  @Debounce(300)
  resize() {
    if (!this.root) {
      window.removeEventListener("resize", this.resize);
      return;
    } else {
      this.setState({
        widthTree: this.root.clientWidth,
        heightTree: this.root.clientHeight,
      });
    }
  }

  handleRoot = (n) => {
    this.root = n;
  };

  componentDidMount() {
    window.addEventListener(
      "resize",
      () => {
        this.requestRef = requestAnimationFrame(() => this.resize());
      },
      { passive: true }
    );
    if (this.root) {
      this.setState({
        widthTree: this.root.clientWidth,
        heightTree: this.root.clientHeight,
      });
    }

    this.props.dispatch(getBuildingAreaAction());
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());

    window.cancelAnimationFrame(this.requestRef);
    window.removeEventListener("resize", this.resize);
    this.resize.cancel();
  }

  componentWillReceiveProps(nextProps) {
    if (
      deepDiffer(
        this.props.settingPlane.buildingArea.lst,
        nextProps.settingPlane.buildingArea.lst
      )
    ) {
      const { lst } = nextProps.settingPlane.buildingArea;
      let gDataList = lst.map((node) => ({
        key: `${node.id}`,
        ...node,
        children: [],
      }));

      const { data } = this.props.buildingCluster;
      let gData = [];

      if (data) {
        gData = parseTree(data, gDataList);

        gDataList = [];
        generateList(gData, gDataList);
      }

      this.setState({
        gDataList,
        gData,
      });
    }
  }

  _remainTree = () => {
    let gData = [...this.state.gData];
    let gDataList = [];
    generateList(gData, gDataList);

    this.setState({
      gData,
      gDataList,
    });
  };

  _removeNewNode = (key, parentKey) => {
    let gData = deleteNode(key, parentKey, this.state.gData);
    let gDataList = [];
    generateList(gData, gDataList);

    this.setState({
      gData: [...gData],
      gDataList,
    });
  };

  renderTitleNode = (d, rootIndex = 0) => {
    const data = d;
    if (!!this.state.adding || !this.state.canCreateOrUpdate) {
      return data.name;
    }
    return (
      <Row
        gutter={24}
        type="flex"
        align="middle"
        justify="space-between"
        className="rowItem"
        style={{
          marginLeft: 0,
          marginRight: 0,
        }}
      >
        <span>
          {data.name}
          <span style={{ marginLeft: 10 }}>
            <Icon
              type="plus-circle"
              style={{
                fontSize: 18,
                cursor: "pointer",
                fontWeight: "bold",
              }}
              onClick={(e) => {
                e.preventDefault();
                e.stopPropagation();
                let newChild = {
                  name: this.props.intl.formatMessage(messages.addNewLevel, {
                    level: rootIndex + 1,
                  }),
                  key: "new-node",
                };
                data.children.unshift(newChild);
                this.setState({
                  adding: {
                    data: undefined,
                    parent: data,
                    level: rootIndex + 1,
                    setState: this.setState.bind(this),
                    remainTree: this._remainTree.bind(this),
                    removeNewNode: this._removeNewNode.bind(this),
                    getExpandKeys: (key) => {
                      return this.expandTreeFrom(
                        key,
                        this.state.gDataList,
                        this.state.gData
                      );
                    },
                  },
                  selectedKeys: ["new-node"],
                  expandedKeys: this.expandTreeFrom(
                    "new-node",
                    [...this.state.gDataList, newChild],
                    this.state.gData
                  ),
                });
              }}
            />
          </span>
        </span>
      </Row>
    );
  };

  _onSelect = (keys, event) => {
    if (keys.length == 0) return;
    this.setState({
      selectedKeys: keys,
    });
  };

  render() {
    const {
      searchValue,
      expandedKeys,
      gData,
      selectedKeys,
      adding,
      gDataList,
    } = this.state;
    const formatMessage = this.props.intl.formatMessage;
    const disableAll = !!adding;
    const loop = (data, iii = 0) =>
      data.map((item) => {
        const title = (
          <Highlighter
            highlightStyle={{ backgroundColor: "#ffc069", padding: 0 }}
            searchWords={[searchValue]}
            autoEscape
            textToHighlight={item.name.toString()}
          />
        );
        if (item.children) {
          return (
            <TreeNode
              selectable={!disableAll}
              key={item.key}
              title={this.renderTitleNode({ ...item, name: title }, iii)}
            >
              {loop(item.children, iii + 1)}
            </TreeNode>
          );
        }
        return (
          <TreeNode selectable={!disableAll} key={item.key} title={title} />
        );
      });

    let nodeSelected = gDataList.find(
      (node) => node.key == selectedKeys[0] && selectedKeys[0] != "-1"
    );

    return (
      <div className={styles.SettingPlane}>
        <Row>
          <Col span={6}>
            <Page inner fixHeight noPadding>
              <div ref={this.handleRoot}>
                <Row
                  style={{
                    borderBottom: "1px solid rgba(202, 203, 212, 0.3)",
                    padding: 16,
                  }}
                >
                  <Col>
                    <Search
                      maxLength={255}
                      disabled={disableAll}
                      placeholder={formatMessage(messages.search)}
                      onChange={this.onChange}
                    />
                  </Col>
                </Row>
                <Row className={styles.parentTree} style={{ marginTop: 16 }}>
                  <Tree
                    onSelect={this._onSelect}
                    onExpand={this.onExpand}
                    expandedKeys={expandedKeys}
                    autoExpandParent
                    selectedKeys={selectedKeys}
                  >
                    {loop(gData)}
                  </Tree>
                </Row>
              </div>
            </Page>
          </Col>
          <Col span={18} style={{ paddingLeft: 16 }}>
            <Page inner fixHeight noPadding>
              <div
                style={{
                  position: "absolute",
                  top: 24,
                  left: 24,
                  right: 24,
                  bottom: 24,
                  overflowY: "scroll",
                }}
              >
                {!!adding && <ClusterCreate adding={adding} {...this.props} />}
                {!adding && !!nodeSelected && (
                  <ClusterInfomation
                    data={nodeSelected}
                    {...this.props}
                    onEdit={() => {
                      this.setState({
                        adding: {
                          data: { ...nodeSelected },
                          level: nodeSelected.level,
                          setState: this.setState.bind(this),
                          remainTree: this._remainTree.bind(this),
                          removeNewNode: this._removeNewNode.bind(this),
                          getExpandKeys: (key) => {
                            return this.expandTreeFrom(
                              key,
                              this.state.gDataList,
                              this.state.gData
                            );
                          },
                        },
                      });
                    }}
                  />
                )}
                {!adding && !nodeSelected && (
                  <BuildingClusterInfomation disableEditable isSettingPlane />
                )}
              </div>
            </Page>
          </Col>
        </Row>
      </div>
    );
  }
}

SettingPlane.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  settingPlane: makeSelectSettingPlane(),
  buildingCluster: selectBuildingCluster(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "settingPlane", reducer });
const withSaga = injectSaga({ key: "settingPlane", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(SettingPlane));
